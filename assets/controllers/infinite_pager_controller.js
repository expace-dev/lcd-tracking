import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static targets = ["link", "sentinel"];

  connect() {
    this.loading = false;

    this.observer = new IntersectionObserver((entries) => {
      const entry = entries[0];
      if (!entry?.isIntersecting) return;
      this.loadNext();
    }, { rootMargin: "300px" });

    if (this.hasSentinelTarget) {
      this.observer.observe(this.sentinelTarget);
    }
  }

  disconnect() {
    this.observer?.disconnect();
  }

  loadNext() {
    if (this.loading) return;
    if (!this.hasLinkTarget) return;

    this.loading = true;
    this.linkTarget.click();

    // petit délai pour éviter double-trigger pendant le replace du pager
    setTimeout(() => { this.loading = false; }, 800);
  }
}