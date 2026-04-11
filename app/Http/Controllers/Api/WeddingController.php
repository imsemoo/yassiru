<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\StoreWeddingRequest;
use App\Models\GroupWedding;
use App\Models\Vendor;
use App\Models\WeddingRegistration;
use App\Notifications\WeddingRegistrationConfirmed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class WeddingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cityId = $request->city_id;
        $page = $request->get('page', 1);
        $cacheKey = "weddings:upcoming:{$cityId}:page:{$page}";

        $weddings = Cache::remember($cacheKey, 1800, function () use ($cityId) {
            $query = GroupWedding::with('city:id,name')
                ->whereIn('status', ['upcoming', 'full']);

            if ($cityId) {
                $query->where('city_id', $cityId);
            }

            return $query->orderBy('wedding_date')->paginate(12);
        });

        return response()->json($weddings);
    }

    public function show(GroupWedding $wedding): JsonResponse
    {
        $wedding->load(['city:id,name', 'registrations.user:id,name']);

        return response()->json($wedding);
    }

    public function register(Request $request, GroupWedding $wedding): JsonResponse
    {
        if ($wedding->status !== 'upcoming') {
            return response()->json(['message' => 'التسجيل مغلق لهذا العرس'], 422);
        }

        if ($wedding->registration_deadline && $wedding->registration_deadline->isPast()) {
            return response()->json(['message' => 'انتهت مهلة التسجيل'], 422);
        }

        if ($wedding->registered_count >= $wedding->max_grooms) {
            return response()->json(['message' => 'العرس مكتمل العدد'], 422);
        }

        if (!$request->user()->has_certificate) {
            return response()->json(['message' => 'يجب الحصول على شهادة التأهيل أولاً'], 403);
        }

        if (WeddingRegistration::where('wedding_id', $wedding->id)->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'أنت مسجل بالفعل في هذا العرس'], 422);
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        WeddingRegistration::create([
            'wedding_id' => $wedding->id,
            'user_id' => $request->user()->id,
            'notes' => $request->notes,
        ]);

        $wedding->increment('registered_count');

        if ($wedding->registered_count >= $wedding->max_grooms) {
            $wedding->update(['status' => 'full']);
        }

        $request->user()->notify(new WeddingRegistrationConfirmed($wedding));

        // Invalidate wedding cache
        Cache::forget("weddings:upcoming:{$wedding->city_id}:page:1");

        return response()->json(['message' => 'تم التسجيل في العرس بنجاح'], 201);
    }

    public function myRegistrations(Request $request): JsonResponse
    {
        $registrations = WeddingRegistration::where('user_id', $request->user()->id)
            ->with('wedding:id,title,venue_name,wedding_date,price_per_groom,status')
            ->latest()
            ->get();

        return response()->json($registrations);
    }

    public function cancelRegistration(Request $request, WeddingRegistration $registration): JsonResponse
    {
        Gate::authorize('cancel', $registration);

        $wedding = $registration->wedding;
        $registration->delete();
        $wedding->decrement('registered_count');

        if ($wedding->status === 'full') {
            $wedding->update(['status' => 'upcoming']);
        }

        return response()->json(['message' => 'تم إلغاء التسجيل']);
    }

    public function pay(Request $request, WeddingRegistration $registration): JsonResponse
    {
        Gate::authorize('pay', $registration);

        $request->validate([
            'payment_ref' => 'required|string|max:255',
        ]);

        $registration->update([
            'payment_status' => 'paid',
            'payment_ref' => $request->payment_ref,
        ]);

        return response()->json(['message' => 'تم تأكيد الدفع بنجاح']);
    }

    public function vendors(Request $request): JsonResponse
    {
        $query = Vendor::where('is_active', true)->with('city:id,name');

        if ($request->city_id) {
            $query->where('city_id', $request->city_id);
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }

        $vendors = $query->orderBy('discount_percent', 'desc')->get();

        return response()->json($vendors);
    }

    // Admin endpoints
    public function adminIndex(): JsonResponse
    {
        $weddings = GroupWedding::with('city:id,name')
            ->withCount('registrations')
            ->latest()
            ->get();

        return response()->json($weddings);
    }

    public function store(StoreWeddingRequest $request): JsonResponse
    {
        $wedding = GroupWedding::create($request->only([
            'city_id', 'title', 'venue_name', 'wedding_date', 'max_grooms',
            'price_per_groom', 'original_price', 'description', 'registration_deadline',
        ]));

        return response()->json(['message' => 'تم إنشاء العرس الجماعي', 'wedding' => $wedding->load('city')], 201);
    }

    public function storeVendor(StoreVendorRequest $request): JsonResponse
    {
        $vendor = Vendor::create($request->validated());

        return response()->json(['message' => 'تم إضافة المورد', 'vendor' => $vendor], 201);
    }
}
