const CURRENCY_CONFIG = {
  EGP: { symbol: 'ج.م', locale: 'ar-EG', name: 'جنيه مصري' },
  SAR: { symbol: 'ر.س', locale: 'ar-SA', name: 'ريال سعودي' },
  AED: { symbol: 'د.إ', locale: 'ar-AE', name: 'درهم إماراتي' },
  KWD: { symbol: 'د.ك', locale: 'ar-KW', name: 'دينار كويتي' },
  JOD: { symbol: 'د.أ', locale: 'ar-JO', name: 'دينار أردني' },
  MAD: { symbol: 'د.م', locale: 'ar-MA', name: 'درهم مغربي' },
  TND: { symbol: 'د.ت', locale: 'ar-TN', name: 'دينار تونسي' },
  DZD: { symbol: 'د.ج', locale: 'ar-DZ', name: 'دينار جزائري' },
  USD: { symbol: '$', locale: 'en-US', name: 'دولار أمريكي' },
}

export function useCurrency() {
  function format(amount, currency = 'EGP') {
    const config = CURRENCY_CONFIG[currency] || CURRENCY_CONFIG.EGP
    const formatted = new Intl.NumberFormat(config.locale, {
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount)

    return `${formatted} ${config.symbol}`
  }

  function getSymbol(currency = 'EGP') {
    return CURRENCY_CONFIG[currency]?.symbol || currency
  }

  function getName(currency = 'EGP') {
    return CURRENCY_CONFIG[currency]?.name || currency
  }

  return { format, getSymbol, getName }
}
