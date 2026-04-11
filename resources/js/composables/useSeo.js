import { useHead } from '@unhead/vue'

/**
 * SEO composable — sets page title, meta tags, OG, and JSON-LD schema
 *
 * @param {Object} options
 * @param {string|null} options.title - Page title (appended with " | يسّرو")
 * @param {string} options.description - Meta description
 * @param {string} options.path - URL path (e.g. "/courses/1")
 * @param {string|null} options.ogImage - Open Graph image URL
 * @param {string} options.type - OG type (default: "website")
 * @param {Object|null} options.schema - JSON-LD structured data object
 */
export function useSeo({
  title = null,
  description = '',
  path = '',
  ogImage = null,
  type = 'website',
  schema = null,
} = {}) {
  const baseUrl = 'https://yassiru.com'
  const fullTitle = title ? `${title} | يسّرو` : 'يسّرو — منصة تيسير الزواج الأولى في العالم الإسلامي'
  const canonicalUrl = `${baseUrl}${path}`
  const image = ogImage || `${baseUrl}/logo.svg`

  const head = {
    title: fullTitle,
    meta: [
      { name: 'description', content: description },
      { property: 'og:title', content: fullTitle },
      { property: 'og:description', content: description },
      { property: 'og:url', content: canonicalUrl },
      { property: 'og:type', content: type },
      { property: 'og:locale', content: 'ar_AR' },
      { property: 'og:site_name', content: 'يسّرو' },
      { property: 'og:image', content: image },
      { name: 'twitter:card', content: 'summary_large_image' },
      { name: 'twitter:title', content: fullTitle },
      { name: 'twitter:description', content: description },
      { name: 'twitter:image', content: image },
    ],
    link: [
      { rel: 'canonical', href: canonicalUrl },
    ],
  }

  if (schema) {
    head.script = [
      {
        type: 'application/ld+json',
        innerHTML: JSON.stringify(schema),
      },
    ]
  }

  useHead(head)
}
