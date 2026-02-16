import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["sentinel", "loader"];
  static values = { url: String };

  connect() {
    this.loading = false;
     this.lastUrl = null;

    this.observer = new IntersectionObserver(
      (entries) => {
        if (!entries[0]?.isIntersecting) return;
        this.load();
      },
      { rootMargin: "800px 0px" }
    );

    if (this.hasSentinelTarget) {
      this.observer.observe(this.sentinelTarget);
    }
  }

  disconnect() {
    this.observer?.disconnect();
  }

  async load() {
    if (this.loading) return;
    if (!this.urlValue) return;
    if (this.urlValue === this.lastUrl) return; // garde-fou anti-boucle

    this.lastUrl = this.urlValue;

    this.loading = true;
    this.observer?.disconnect();

    if (this.hasLoaderTarget) this.loaderTarget.hidden = false;

    try {
      const response = await fetch(this.urlValue, {
        headers: {
          "Turbo-Stream": "true",
          "Accept": "text/vnd.turbo-stream.html",
        },
        credentials: "same-origin",
      });

      if (!response.ok) throw new Error(`HTTP ${response.status}`);

      const html = await response.text();

      if (window.Turbo?.renderStreamMessage) {
        window.Turbo.renderStreamMessage(html);
      }
    } catch (e) {
      // En cas d'erreur, on stoppe silencieusement (page protégée)
      // console.warn("Infinite pager failed:", e);
    } finally {
      this.loading = false;
    }
  }
}
