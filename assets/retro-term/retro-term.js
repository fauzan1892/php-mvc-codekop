(function () {
  const RT = window.RT || (window.RT = {});

  function isExternalUrl(value) {
    return /^(?:[a-z][a-z\d+\-.]*:|\/\/|#|mailto:|tel:|javascript:)/i.test(
      value || "",
    );
  }

  function normalizeBaseUrl(value) {
    if (!value) return "";
    const trimmed = String(value).trim();
    if (!trimmed) return "";

    try {
      const parsed = new URL(trimmed, window.location.href);
      return parsed.href.replace(/[#?].*$/, "").replace(/\/?$/, "/");
    } catch {
      return trimmed.endsWith("/") ? trimmed : `${trimmed}/`;
    }
  }

  RT.config = RT.config || {};
  RT.config.baseUrl = normalizeBaseUrl(
    RT.config.baseUrl ||
      document.documentElement.getAttribute("data-base-url") ||
      window.RT_BASE_URL ||
      localStorage.getItem("rt-base-url") ||
      "",
  );

  RT.url = function (path) {
    if (!path || isExternalUrl(path)) return path;
    if (!RT.config.baseUrl) return path;

    try {
      return new URL(path, RT.config.baseUrl).href;
    } catch {
      return path;
    }
  };

  RT.baseUrl = function (path) {
    return RT.url(path);
  };

  RT.bindBaseUrls = function (root = document) {
    root.querySelectorAll("a[href]").forEach((el) => {
      const value = el.getAttribute("href");
      if (!value || isExternalUrl(value)) return;
      el.setAttribute("href", RT.url(value));
    });

    root.querySelectorAll("[data-rt-href]").forEach((el) => {
      const value = el.getAttribute("data-rt-href");
      if (value) el.setAttribute("href", RT.url(value));
    });

    root.querySelectorAll("[data-rt-src]").forEach((el) => {
      const value = el.getAttribute("data-rt-src");
      if (value) el.setAttribute("src", RT.url(value));
    });

    root.querySelectorAll("[data-rt-action]").forEach((el) => {
      const value = el.getAttribute("data-rt-action");
      if (value) el.setAttribute("action", RT.url(value));
    });
  };

  function initRetroTerm() {
    const html = document.documentElement;
    RT.bindBaseUrls();

    // ===== THEME TOGGLE =====
    const themeToggles = document.querySelectorAll(
      '#themeToggle, [data-rt-theme-toggle], [data-theme-toggle]',
    );
    const themeToggle = themeToggles[0];
    const moonIcon = themeToggle?.querySelector(".moon-icon");
    const sunIcon = themeToggle?.querySelector(".sun-icon");

    function syncThemeIcons(theme) {
      if (!moonIcon || !sunIcon) return;
      if (theme === "dark") {
        moonIcon.style.display = "none";
        sunIcon.style.display = "block";
      } else {
        moonIcon.style.display = "block";
        sunIcon.style.display = "none";
      }
    }

    function applyTheme(theme) {
      if (typeof window.setTheme === "function") {
        window.setTheme(theme);
      } else {
        html.setAttribute("data-theme", theme);
        localStorage.setItem("theme", theme);
        localStorage.setItem("rt-theme", theme);
      }
      syncThemeIcons(theme);
    }

    const initialTheme =
      html.getAttribute("data-theme") ||
      localStorage.getItem("theme") ||
      localStorage.getItem("rt-theme") ||
      "dark";

    applyTheme(initialTheme);

    themeToggles.forEach((toggle) => {
      if (toggle.dataset.rtThemeBound === "true") return;
      toggle.dataset.rtThemeBound = "true";
      toggle.addEventListener("click", () => {
        if (typeof window.toggleTheme === "function") {
          window.toggleTheme();
        } else {
          const current = html.getAttribute("data-theme");
          applyTheme(current === "dark" ? "light" : "dark");
        }

        syncThemeIcons(html.getAttribute("data-theme") || "light");
      });
    });

    // ===== SHAPE MODE (flat / rounded) =====
    const shapeToggles = document.querySelectorAll(
      '#radiusToggle, #shapeToggle, [data-rt-radius-toggle], [data-rt-shape-toggle], [data-radius-toggle]',
    );

    function applyShape(shape) {
      const normalized = shape === "rounded" ? "rounded" : "flat";
      html.setAttribute("data-radius", normalized);
      html.setAttribute("data-shape", normalized);
      localStorage.setItem("rt-radius", normalized);
      localStorage.setItem("rt-shape", normalized);
      document.querySelectorAll("[data-rt-shape-value]").forEach((node) => {
        node.textContent = normalized;
      });
    }

    applyShape(
      html.getAttribute("data-radius") ||
        html.getAttribute("data-shape") ||
        localStorage.getItem("rt-radius") ||
        localStorage.getItem("rt-shape") ||
        "flat",
    );

    // Application shells may already own the legacy radius hooks. Avoid
    // binding a second click handler, which would toggle twice per click.
    const hasExternalShapeApi =
      typeof window.setRadius === "function" ||
      typeof window.toggleRadius === "function";

    if (!hasExternalShapeApi) {
      shapeToggles.forEach((toggle) => {
        if (toggle.dataset.rtShapeBound === "true") return;
        toggle.dataset.rtShapeBound = "true";
        toggle.addEventListener("click", () => {
          const current = html.getAttribute("data-radius") || "flat";
          applyShape(current === "rounded" ? "flat" : "rounded");
        });
      });
    }

    // ===== SIDEBAR TOGGLE (mobile) =====
    const menuBtn = document.getElementById("menuBtn");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    menuBtn?.addEventListener("click", () => {
      sidebar?.classList.toggle("is-open");
      overlay?.classList.toggle("is-open");
    });

    overlay?.addEventListener("click", () => {
      sidebar?.classList.remove("is-open");
      overlay?.classList.remove("is-open");
    });

    // ===== SIDEBAR NAV ACTIVE =====
    function syncSidebarState() {
      const currentFile =
        window.location.pathname.split("/").pop()?.toLowerCase() ||
        "index.html";
      const navLinks = document.querySelectorAll(".rt-sbr-link");
      const childLinks = document.querySelectorAll(".rt-nav-dropdown-item");
      const dropdowns = document.querySelectorAll(
        "[data-rt-nav-dropdown], .rt-nav-dropdown",
      );

      navLinks.forEach((link) => link.classList.remove("is-active"));
      childLinks.forEach((child) => child.classList.remove("is-active"));
      dropdowns.forEach((sd) => {
        sd.classList.remove("is-open");
        sd.classList.remove("show");
      });

      navLinks.forEach((link) => {
        const href = link.getAttribute("href");
        if (!href || href === "#") return;
        const target = href.split("/").pop()?.toLowerCase();
        if (target && target === currentFile) {
          link.classList.add("is-active");
        }
      });

      childLinks.forEach((child) => {
        const href = child.getAttribute("href");
        if (!href || href === "#") return;
        const target = href.split("/").pop()?.toLowerCase();
        if (target && target === currentFile) {
          child.classList.add("is-active");
          const parent = child.closest(
            "[data-rt-nav-dropdown], .rt-nav-dropdown",
          );
          parent?.classList.add("is-open");
        }
      });
    }

    document.querySelectorAll(".rt-sbr-link").forEach((link) => {
      link.addEventListener("click", (e) => {
        const href = link.getAttribute("href");
        if (!href || href === "#") return;
        document
          .querySelectorAll(".rt-sbr-link")
          .forEach((l) => l.classList.remove("is-active"));
        link.classList.add("is-active");
        if (window.innerWidth <= 1024) {
          sidebar?.classList.remove("is-open");
          overlay?.classList.remove("is-open");
        }
      });
    });

    // ===== SIDEBAR DROPDOWN (rt-nav-dropdown) =====
    document
      .querySelectorAll("[data-rt-nav-dropdown], .rt-nav-dropdown")
      .forEach((sd) => {
        const trigger = sd.querySelector(".rt-nav-dropdown-toggle");
        trigger?.addEventListener("click", (e) => {
          e.preventDefault();
          const willOpen = !sd.classList.contains("is-open");
          sd.classList.toggle("is-open", willOpen);
          sd.classList.toggle("show", willOpen);
        });
      });

    document.querySelectorAll(".rt-nav-dropdown-item").forEach((child) => {
      child.addEventListener("click", (e) => {
        if (window.innerWidth <= 1024) {
          sidebar?.classList.remove("is-open");
          overlay?.classList.remove("is-open");
        }
      });
    });

    // ===== DROPDOWN (rt-dropdown) =====
    document
      .querySelectorAll("[data-rt-dropdown], .rt-dropdown")
      .forEach((dd) => {
        const trigger = dd.querySelector("[data-rt-dropdown-trigger]");
        trigger?.addEventListener("click", (e) => {
          e.stopPropagation();
          document
            .querySelectorAll("[data-rt-dropdown], .rt-dropdown")
            .forEach((o) => {
              if (o !== dd) o.classList.remove("is-open");
              if (o !== dd) o.classList.remove("show");
            });
          const willOpen = !dd.classList.contains("is-open");
          dd.classList.toggle("is-open", willOpen);
          dd.classList.toggle("show", willOpen);
        });
      });

    document.addEventListener("click", (e) => {
      if (!e.target.closest("[data-rt-dropdown], .rt-dropdown")) {
        document
          .querySelectorAll("[data-rt-dropdown], .rt-dropdown")
          .forEach((dd) => {
            dd.classList.remove("is-open");
            dd.classList.remove("show");
          });
      }
    });

    // ===== SEARCHABLE SELECTS =====
    function initSearchableSelects() {
      const selectNodes = document.querySelectorAll("select.rt-form-select");

      selectNodes.forEach((select, index) => {
        if (select.dataset.rtSelectReady === "true" || select.multiple) return;
        select.dataset.rtSelectReady = "true";

        const wrapper = document.createElement("div");
        wrapper.className = "rt-select";

        const toggle = document.createElement("button");
        toggle.type = "button";
        toggle.className = "rt-select-toggle";
        toggle.setAttribute("aria-haspopup", "listbox");
        toggle.setAttribute("aria-expanded", "false");

        const value = document.createElement("span");
        value.className = "rt-select-value";

        const caret = document.createElement("span");
        caret.className = "rt-select-caret";
        caret.setAttribute("aria-hidden", "true");

        toggle.append(value, caret);

        const menu = document.createElement("div");
        menu.className = "rt-select-menu";
        menu.hidden = true;

        const search = document.createElement("div");
        search.className = "rt-select-search";

        const searchIcon = document.createElement("span");
        searchIcon.className = "rt-select-search-icon";
        searchIcon.setAttribute("aria-hidden", "true");

        const searchInput = document.createElement("input");
        searchInput.type = "search";
        searchInput.className = "rt-select-search-input";
        searchInput.placeholder =
          select.getAttribute("data-search-placeholder") || "Cari opsi...";
        searchInput.autocomplete = "off";
        searchInput.spellcheck = false;

        search.append(searchIcon, searchInput);

        const list = document.createElement("div");
        list.className = "rt-select-list";
        list.setAttribute("role", "listbox");

        menu.append(search, list);

        select.parentNode.insertBefore(wrapper, select);
        wrapper.append(select, toggle);
        document.body.appendChild(menu);
        select.classList.add("rt-select-native");

        let isOpen = false;
        let lastScrollX = window.scrollX;
        let lastScrollY = window.scrollY;

        function positionMenu() {
          if (menu.hidden) return;
          const rect = toggle.getBoundingClientRect();
          const gap = 8;
          const viewportPadding = 12;
          const minWidth = 180;
          const desiredWidth = Math.max(rect.width, minWidth);
          const maxWidth = Math.max(0, window.innerWidth - viewportPadding * 2);
          const width = Math.min(desiredWidth, maxWidth || desiredWidth);
          const left = Math.min(
            Math.max(viewportPadding, rect.left),
            Math.max(
              viewportPadding,
              window.innerWidth - width - viewportPadding,
            ),
          );
          const spaceBelow =
            window.innerHeight - rect.bottom - gap - viewportPadding;
          const spaceAbove = rect.top - gap - viewportPadding;
          const openUp = spaceBelow < 220 && spaceAbove > spaceBelow;
          const top = openUp
            ? Math.max(viewportPadding, rect.top - gap)
            : Math.min(window.innerHeight - viewportPadding, rect.bottom + gap);

          menu.style.position = "fixed";
          menu.style.left = `${left}px`;
          menu.style.top = openUp ? `${top}px` : `${top}px`;
          menu.style.width = `${width}px`;
          menu.style.maxWidth = `${maxWidth || width}px`;
          menu.style.transform = openUp ? "translateY(-100%)" : "none";
          menu.dataset.position = openUp ? "top" : "bottom";
        }

        function getSelectedLabel() {
          const selected = select.options[select.selectedIndex];
          if (!selected)
            return select.getAttribute("data-placeholder") || "Pilih opsi";
          const text = (selected.textContent || "").trim();
          return (
            text || select.getAttribute("data-placeholder") || "Pilih opsi"
          );
        }

        function renderOptions(query = "") {
          list.innerHTML = "";
          const term = query.trim().toLowerCase();
          let matchCount = 0;

          Array.from(select.options).forEach((option) => {
            if (option.hidden) return;
            const text = (option.textContent || "").trim();
            const haystack = `${text} ${option.value || ""}`.toLowerCase();
            if (term && !haystack.includes(term)) return;

            matchCount += 1;
            const item = document.createElement("button");
            item.type = "button";
            item.className = "rt-select-option";
            item.textContent = text || option.value || "";
            item.disabled = option.disabled;
            item.dataset.value = option.value;
            item.setAttribute("role", "option");
            item.setAttribute(
              "aria-selected",
              option.selected ? "true" : "false",
            );
            if (option.selected) item.classList.add("is-selected");

            item.addEventListener("click", () => {
              if (option.disabled) return;
              select.value = option.value;
              select.dispatchEvent(new Event("change", { bubbles: true }));
              syncFromSelect();
              closeMenu();
            });

            list.append(item);
          });

          if (!matchCount) {
            const empty = document.createElement("div");
            empty.className = "rt-select-empty";
            empty.textContent = "Tidak ada opsi yang cocok";
            list.append(empty);
          }
        }

        async function loadRemoteOptions(query) {
          const endpoint = select.getAttribute("data-ajax-url") || select.getAttribute("data-search-url");
          if (!endpoint) return;
          const url = new URL(endpoint, window.location.href);
          url.searchParams.set("q", query);
          url.searchParams.set("search", query);
          try {
            const response = await fetch(url.href, { headers: { Accept: "application/json" } });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const payload = await response.json();
            const options = Array.isArray(payload) ? payload : payload.options;
            if (!Array.isArray(options)) return;
            select.querySelectorAll("option[data-rt-remote]").forEach((option) => option.remove());
            options.forEach((item) => {
              const option = document.createElement("option");
              option.dataset.rtRemote = "true";
              option.value = typeof item === "object" ? item.value : item;
              option.textContent = typeof item === "object" ? (item.label ?? item.text ?? item.value) : item;
              if (typeof item === "object" && item.disabled) option.disabled = true;
              select.appendChild(option);
            });
            syncFromSelect();
          } catch (error) {
            select.dispatchEvent(new CustomEvent("rt:select:error", { detail: error }));
          }
        }

        function syncFromSelect() {
          value.textContent = getSelectedLabel();
          toggle.title = value.textContent;
          toggle.disabled = select.disabled;
          renderOptions(searchInput.value);
        }

        function openMenu() {
          if (select.disabled) return;
          document.querySelectorAll(".rt-select.is-open").forEach((node) => {
            if (node !== wrapper)
              node.querySelector(".rt-select-toggle")?.click();
          });
          menu.hidden = false;
          document.body.appendChild(menu);
          wrapper.classList.add("is-open");
          toggle.setAttribute("aria-expanded", "true");
          isOpen = true;
          searchInput.value = "";
          renderOptions("");
          positionMenu();
          window.requestAnimationFrame(() => searchInput.focus());
        }

        function closeMenu() {
          if (!isOpen) return;
          menu.hidden = true;
          wrapper.classList.remove("is-open");
          toggle.setAttribute("aria-expanded", "false");
          menu.style.removeProperty("position");
          menu.style.removeProperty("left");
          menu.style.removeProperty("top");
          menu.style.removeProperty("width");
          menu.style.removeProperty("max-width");
          menu.style.removeProperty("transform");
          searchInput.value = "";
          isOpen = false;
        }

        toggle.addEventListener("click", () => {
          if (menu.hidden) {
            openMenu();
          } else {
            closeMenu();
          }
        });

        toggle.addEventListener("keydown", (e) => {
          if (e.key === "ArrowDown" || e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            openMenu();
          }

          if (e.key === "Escape") {
            closeMenu();
          }
        });

        searchInput.addEventListener("input", () => {
          renderOptions(searchInput.value);
          window.clearTimeout(searchInput._rtRemoteTimer);
          searchInput._rtRemoteTimer = window.setTimeout(
            () => loadRemoteOptions(searchInput.value.trim()),
            180,
          );
        });

        searchInput.addEventListener("keydown", (e) => {
          if (e.key === "Escape") {
            e.stopPropagation();
            closeMenu();
            toggle.focus();
          }
        });

        select.addEventListener("change", syncFromSelect);
        select.addEventListener("input", syncFromSelect);

        document.addEventListener("click", (e) => {
          if (!wrapper.contains(e.target) && !menu.contains(e.target)) {
            closeMenu();
          }
        });

        document.addEventListener("keydown", (e) => {
          if (e.key === "Escape") {
            closeMenu();
          }
        });

        window.addEventListener("resize", () => {
          if (isOpen) positionMenu();
        });

        window.addEventListener(
          "scroll",
          () => {
            if (!isOpen) return;
            if (
              window.scrollX !== lastScrollX ||
              window.scrollY !== lastScrollY
            ) {
              lastScrollX = window.scrollX;
              lastScrollY = window.scrollY;
              positionMenu();
            }
          },
          true,
        );

        syncFromSelect();
      });
    }

    initSearchableSelects();

    syncSidebarState();

    // ===== POPUP (rt-modal) =====
    const popupCloseDelay = 220;

    function openPopup(id) {
      const pp = document.getElementById(id);
      if (!pp) return;
      pp.classList.remove("is-closing");
      pp.classList.add("is-open");
      pp.classList.add("show");
      document.body.classList.add("rt-modal-open");
    }

    function closePopup(pp) {
      if (
        !pp ||
        !pp.classList.contains("is-open") ||
        pp.classList.contains("is-closing")
      )
        return;
      pp.classList.add("is-closing");
      const panel = pp.querySelector(".rt-modal-content, .rt-modal-dialog");
      const finish = () => {
        pp.classList.remove("is-open", "is-closing", "show");
        document.body.classList.remove("rt-modal-open");
      };

      if (panel) {
        panel.addEventListener("animationend", function onEnd(e) {
          if (e.target !== panel) return;
          panel.removeEventListener("animationend", onEnd);
          finish();
        });
      }

      window.setTimeout(finish, popupCloseDelay);
    }

    document.querySelectorAll("[data-rt-modal-open]").forEach((btn) => {
      btn.addEventListener("click", () =>
        openPopup(btn.getAttribute("data-rt-modal-open")),
      );
    });

    document.querySelectorAll("[data-rt-modal-close]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const pp = btn.closest("[data-rt-modal]");
        if (pp) closePopup(pp);
      });
    });

    document.querySelectorAll("[data-rt-modal]").forEach((pp) => {
      pp.addEventListener("click", (e) => {
        if (e.target === pp) closePopup(pp);
      });
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        document
          .querySelectorAll("[data-rt-modal].is-open")
          .forEach(closePopup);
      }
    });

    // ===== TOAST =====
    const toast = document.getElementById("demoToast");
    const toastCloseBtn = document.getElementById("toastCloseBtn");
    let toastTimer = null;
    let toastVariant = "success";
    let toastPosition = "bottom-right";

    function applyToastState() {
      if (!toast) return;
      toast.classList.remove(
        "rt-toast--success",
        "rt-toast--warning",
        "rt-toast--danger",
        "rt-toast--top-left",
        "rt-toast--top-center",
        "rt-toast--top-right",
        "rt-toast--bottom-left",
        "rt-toast--bottom-center",
        "rt-toast--bottom-right",
      );
      toast.classList.add(
        `rt-toast--${toastVariant}`,
        `rt-toast--${toastPosition}`,
      );
    }

    function hideToast() {
      if (!toast) return;
      toast.classList.remove("is-open");
      window.clearTimeout(toastTimer);
      toastTimer = null;
    }

    function showToast(variant = toastVariant, position = toastPosition) {
      if (!toast) return;
      toastVariant = variant;
      toastPosition = position;
      applyToastState();

      const titleMap = {
        success: "Toast Success",
        warning: "Toast Warning",
        danger: "Toast Danger",
      };
      const textMap = {
        success: "Action was saved successfully and is ready to continue.",
        warning: "There is something to check before continuing.",
        danger: "This action is risky and needs attention.",
      };

      const titleEl = toast.querySelector(".rt-toast-title");
      const textEl = toast.querySelector(".rt-toast-text");
      if (titleEl) titleEl.textContent = titleMap[variant] || "Toast";
      if (textEl) textEl.textContent = textMap[variant] || "Pesan toast.";

      toast.classList.add("is-open");
      window.clearTimeout(toastTimer);
      toastTimer = window.setTimeout(hideToast, 3000);
    }

    document.querySelectorAll("[data-rt-toast-variant]").forEach((btn) => {
      btn.addEventListener("click", () => {
        showToast(
          btn.getAttribute("data-rt-toast-variant"),
          btn.getAttribute("data-rt-toast-position") || toastPosition,
        );
      });
    });

    document.querySelectorAll("[data-rt-toast-position-btn]").forEach((btn) => {
      btn.addEventListener("click", () => {
        showToast(toastVariant, btn.getAttribute("data-rt-toast-position"));
      });
    });

    toastCloseBtn?.addEventListener("click", hideToast);
    applyToastState();

    // ===== ACCORDION =====
    document.querySelectorAll("[data-accordion]").forEach((accordion) => {
      const isMultiple = accordion.hasAttribute("data-accordion-multiple");
      accordion.querySelectorAll(".rt-accordion-trigger").forEach((trigger) => {
        trigger.addEventListener("click", () => {
          const item = trigger.closest(".rt-accordion-item");
          if (!item) return;
          const isOpen = item.classList.contains("is-open");

          if (!isMultiple) {
            accordion.querySelectorAll(".rt-accordion-item").forEach((i) => {
              i.classList.remove("is-open");
              i.querySelector(".rt-accordion-trigger")?.classList.remove(
                "is-active",
              );
            });
          }

          if (!isOpen) {
            item.classList.add("is-open");
            trigger.classList.add("is-active");
          }
        });
      });
    });

    // ===== CAROUSEL =====
    document.querySelectorAll("[data-carousel]").forEach((carousel) => {
      const track = carousel.querySelector(".rt-carousel-track");
      const prevBtn = carousel.querySelector("[data-carousel-prev]");
      const nextBtn = carousel.querySelector("[data-carousel-next]");
      const indicators = carousel.querySelectorAll(".rt-carousel-indicator");
      let currentIndex = 0;
      let slideCount = carousel.querySelectorAll(".rt-carousel-slide").length;
      let autoPlayTimer = null;
      const autoPlayDelay = carousel.getAttribute("data-carousel-autoplay");

      function goToSlide(index) {
        if (index < 0) index = slideCount - 1;
        if (index >= slideCount) index = 0;
        currentIndex = index;

        if (track) {
          track.style.transform = `translateX(-${currentIndex * 100}%)`;
        }

        indicators.forEach((ind, i) => {
          ind.classList.toggle("is-active", i === currentIndex);
        });
      }

      function nextSlide() {
        goToSlide(currentIndex + 1);
      }

      function prevSlide() {
        goToSlide(currentIndex - 1);
      }

      function startAutoPlay() {
        if (!autoPlayDelay) return;
        stopAutoPlay();
        autoPlayTimer = setInterval(nextSlide, parseInt(autoPlayDelay));
      }

      function stopAutoPlay() {
        if (autoPlayTimer) {
          clearInterval(autoPlayTimer);
          autoPlayTimer = null;
        }
      }

      prevBtn?.addEventListener("click", () => {
        prevSlide();
        stopAutoPlay();
        startAutoPlay();
      });

      nextBtn?.addEventListener("click", () => {
        nextSlide();
        stopAutoPlay();
        startAutoPlay();
      });

      indicators.forEach((ind, index) => {
        ind.addEventListener("click", () => {
          goToSlide(index);
          stopAutoPlay();
          startAutoPlay();
        });
      });

      carousel.addEventListener("mouseenter", stopAutoPlay);
      carousel.addEventListener("mouseleave", startAutoPlay);

      goToSlide(0);
      startAutoPlay();
    });

    // ===== NAVBAR SCROLL EFFECT =====
    const navbar = document.querySelector(".rt-navbar");
    if (navbar) {
      function handleScroll() {
        if (window.scrollY > 10) {
          navbar.classList.add("rt-navbar--scrolled");
        } else {
          navbar.classList.remove("rt-navbar--scrolled");
        }
      }
      window.addEventListener("scroll", handleScroll);
      handleScroll();

      // Mobile navbar toggle
      const navbarToggle = navbar.querySelector(".rt-navbar-toggle");
      const navbarMenu = navbar.querySelector(".rt-navbar-menu");
      navbarToggle?.addEventListener("click", () => {
        navbarToggle.classList.toggle("is-open");
        navbarMenu?.classList.toggle("is-open");
      });
    }

    // ===== FORM VALIDATION =====
    document.querySelectorAll("[data-validate]").forEach((form) => {
      form.addEventListener("submit", (e) => {
        let isValid = true;

        form.querySelectorAll("[data-rule]").forEach((field) => {
          const value = field.value.trim();
          const rules = field.getAttribute("data-rule").split("|");
          const formGroup = field.closest(".rt-form-group");

          // Clear previous states
          field.classList.remove("is-valid", "is-invalid");
          formGroup?.classList.remove("is-valid", "is-invalid");

          let errorMessage = "";

          rules.forEach((rule) => {
            if (rule === "required" && !value) {
              errorMessage = "This field is required";
            } else if (
              rule === "email" &&
              value &&
              !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)
            ) {
              errorMessage = "Please enter a valid email";
            } else if (
              rule.startsWith("minlength:") &&
              value.length < parseInt(rule.split(":")[1])
            ) {
              errorMessage = `Minimum ${rule.split(":")[1]} characters required`;
            } else if (
              rule.startsWith("maxlength:") &&
              value.length > parseInt(rule.split(":")[1])
            ) {
              errorMessage = `Maximum ${rule.split(":")[1]} characters allowed`;
            }
          });

          if (errorMessage) {
            field.classList.add("is-invalid");
            formGroup?.classList.add("is-invalid");
            isValid = false;

            const messageEl = formGroup?.querySelector(
              ".rt-form-message--error",
            );
            if (messageEl) {
              messageEl.textContent = errorMessage;
              messageEl.classList.add("is-visible");
            }
          } else if (value) {
            field.classList.add("is-valid");
            formGroup?.classList.add("is-valid");

            const successEl = formGroup?.querySelector(
              ".rt-form-message--success",
            );
            if (successEl) {
              successEl.classList.add("is-visible");
            }
          }
        });

        if (!isValid) {
          e.preventDefault();
          e.stopPropagation();
        }
      });

      // Real-time validation on blur
      form.querySelectorAll("[data-rule]").forEach((field) => {
        field.addEventListener("blur", () => {
          const value = field.value.trim();
          const rules = field.getAttribute("data-rule")?.split("|") || [];
          const formGroup = field.closest(".rt-form-group");

          field.classList.remove("is-valid", "is-invalid");
          formGroup?.classList.remove("is-valid", "is-invalid");

          let errorMessage = "";

          rules.forEach((rule) => {
            if (rule === "required" && !value) {
              errorMessage = "This field is required";
            } else if (
              rule === "email" &&
              value &&
              !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)
            ) {
              errorMessage = "Please enter a valid email";
            }
          });

          if (errorMessage) {
            field.classList.add("is-invalid");
            formGroup?.classList.add("is-invalid");
            const messageEl = formGroup?.querySelector(
              ".rt-form-message--error",
            );
            if (messageEl) {
              messageEl.textContent = errorMessage;
              messageEl.classList.add("is-visible");
            }
          } else if (value) {
            field.classList.add("is-valid");
            formGroup?.classList.add("is-valid");
          }
        });
      });
    });

    // ===== SUBMIT LOADING =====
    const ensureSubmitSpinner = (button) => {
      if (!button) return null;

      let spinner = button.querySelector(".rt-spinner");
      if (!spinner) {
        spinner = document.createElement("span");
        spinner.className = "rt-spinner";
        spinner.setAttribute("aria-hidden", "true");
        button.insertBefore(spinner, button.firstChild);
      }

      return spinner;
    };

    document.querySelectorAll("form").forEach((form) => {
      form.addEventListener("submit", (e) => {
        if (e.defaultPrevented) return;

        const submitter =
          e.submitter ||
          form.querySelector('button[type="submit"], input[type="submit"]');

        if (!(submitter instanceof HTMLElement)) return;

        const spinner = ensureSubmitSpinner(submitter);
        spinner?.classList.add("is-active");
        submitter.classList.add("is-loading");
        submitter.setAttribute("aria-busy", "true");
        submitter.setAttribute("disabled", "disabled");
      });
    });

    // ===== STANDARD TABLE with Search, Sort, Page Size and Pagination =====
    function initStandardTables() {
      document.querySelectorAll("table.rt-table").forEach((table) => {
        if (table.dataset.rtTableReady === "true") return;
        const isServerTable = table.hasAttribute("data-rt-table-server");
        // The documentation demo has a custom renderer below; keep it intact.
        if (table.querySelector("tbody#tableBody")) return;

        const body = table.tBodies[0];
        if (!body) return;

        const existingScroll = table.closest(".rt-table-responsive");
        const host = table.closest("[data-rt-table]") ||
          (existingScroll ? existingScroll.parentElement : table.parentElement);
        if (!host) return;
        table.dataset.rtTableReady = "true";
        host.classList.add("rt-table-component");

        let scroll = existingScroll;
        if (!scroll) {
          scroll = document.createElement("div");
          scroll.className = "rt-table-responsive";
          table.parentNode.insertBefore(scroll, table);
          scroll.appendChild(table);
        }

        let toolbar = host.querySelector(".rt-table-toolbar");
        if (!toolbar) {
          toolbar = document.createElement("div");
          toolbar.className = "rt-table-toolbar";
          const toolbarParent = scroll.parentElement || host;
          toolbarParent.insertBefore(toolbar, scroll);
        }

        const left = toolbar.querySelector(".rt-table-toolbar-left") || document.createElement("div");
        left.className = "rt-table-toolbar-left";
        if (!left.parentNode) toolbar.appendChild(left);

        let pageSize = left.querySelector(".rt-table-page-size");
        if (!pageSize) {
          pageSize = document.createElement("select");
          pageSize.className = "rt-table-page-size";
          pageSize.setAttribute("aria-label", "Jumlah baris per halaman");
          pageSize.innerHTML = '<option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="0">Semua</option>';
          left.appendChild(pageSize);
        }
        if (!pageSize.id) pageSize.id = `${table.id || "rt-table"}-page-size`;
        if (!left.querySelector(".rt-table-page-size-label")) {
          const label = document.createElement("label");
          label.className = "rt-table-page-size-label rt-text-muted";
          label.htmlFor = pageSize.id;
          label.textContent = "Rows";
          left.insertBefore(label, pageSize);
        }

        const right = toolbar.querySelector(".rt-table-toolbar-right") || document.createElement("div");
        right.className = "rt-table-toolbar-right";
        if (!right.parentNode) toolbar.appendChild(right);

        let search = right.querySelector(".rt-table-search");
        if (!search) {
          search = document.createElement("label");
          search.className = "rt-table-search";
          search.innerHTML = '<span class="rt rt-search" aria-hidden="true"></span><input type="search" autocomplete="off" placeholder="Search..." aria-label="Search table data">';
          right.appendChild(search);
        }
        const searchInput = search.querySelector("input");
        if (searchInput && !searchInput.id) searchInput.id = `${table.id || "rt-table"}-search`;

        let pagination = host.querySelector(":scope > .rt-table-pagination");
        if (!pagination) {
          pagination = document.createElement("div");
          pagination.className = "rt-table-pagination";
          host.appendChild(pagination);
        }
        let info = pagination.querySelector(".rt-table-pagination-info");
        if (!info) {
          info = document.createElement("div");
          info.className = "rt-table-pagination-info";
          pagination.appendChild(info);
        }
        let nav = pagination.querySelector(".rt-table-pagination-nav");
        if (!nav) {
          nav = document.createElement("div");
          nav.className = "rt-table-pagination-nav";
          pagination.appendChild(nav);
        }
        nav.innerHTML = '<button class="rt-table-pagination-btn" type="button" data-page="prev" aria-label="Halaman sebelumnya">‹</button><span class="rt-table-pagination-text"></span><button class="rt-table-pagination-btn" type="button" data-page="next" aria-label="Halaman berikutnya">›</button>';
        const pageText = nav.querySelector(".rt-table-pagination-text");
        const previous = nav.querySelector('[data-page="prev"]');
        const next = nav.querySelector('[data-page="next"]');
        table.dispatchEvent(new CustomEvent("rt:table:ready", { detail: { pageSize, searchInput, pagination, pageText, previous, next } }));
        if (isServerTable) return;
        const originalRows = Array.from(body.rows).map((row) => row.cloneNode(true));
        const pageSizeFromMarkup = Number(table.dataset.pageSize || host.dataset.pageSize || 10);
        pageSize.value = [10, 25, 50, 100, 0].includes(pageSizeFromMarkup) ? String(pageSizeFromMarkup) : "10";
        let currentPage = 1;
        let sortColumn = -1;
        let sortDirection = 1;

        const getRows = () => {
          const query = (searchInput?.value || "").trim().toLowerCase();
          let rows = originalRows.filter((row) => !query || row.textContent.toLowerCase().includes(query));
          if (sortColumn >= 0) {
            rows = rows.sort((a, b) => {
              const leftText = a.cells[sortColumn]?.textContent.trim().toLowerCase() || "";
              const rightText = b.cells[sortColumn]?.textContent.trim().toLowerCase() || "";
              return leftText.localeCompare(rightText, undefined, { numeric: true }) * sortDirection;
            });
          }
          return rows;
        };

        const render = () => {
          const rows = getRows();
          const limit = Number(pageSize.value);
          const totalPages = limit === 0 ? 1 : Math.max(1, Math.ceil(rows.length / limit));
          currentPage = Math.min(currentPage, totalPages);
          const start = limit === 0 ? 0 : (currentPage - 1) * limit;
          const visible = limit === 0 ? rows : rows.slice(start, start + limit);
          body.replaceChildren(...visible.map((row) => row.cloneNode(true)));
          const end = rows.length === 0 ? 0 : (limit === 0 ? rows.length : Math.min(start + limit, rows.length));
          info.textContent = `Showing ${rows.length === 0 ? 0 : start + 1}-${end} of ${rows.length} items`;
          pageText.textContent = `Page ${currentPage} / ${totalPages}`;
          previous.disabled = currentPage <= 1;
          next.disabled = currentPage >= totalPages;
          table.dispatchEvent(new CustomEvent("rt:table:render", { detail: { page: currentPage, pageSize: limit, total: rows.length, query: searchInput?.value || "" } }));
        };

        table.querySelectorAll("thead th").forEach((th, index) => {
          if (th.hasAttribute("data-no-sort")) return;
          th.classList.add("rt-table-sortable");
          th.setAttribute("tabindex", "0");
          const sort = () => {
            if (sortColumn === index) sortDirection *= -1;
            else { sortColumn = index; sortDirection = 1; }
            currentPage = 1;
            render();
          };
          th.addEventListener("click", sort);
          th.addEventListener("keydown", (event) => {
            if (event.key === "Enter" || event.key === " ") { event.preventDefault(); sort(); }
          });
        });
        searchInput?.addEventListener("input", () => {
          currentPage = 1;
          window.clearTimeout(table._rtSearchTimer);
          table._rtSearchTimer = window.setTimeout(() => {
            table.dispatchEvent(new CustomEvent("rt:table:search", { detail: { query: searchInput.value } }));
            render();
          }, 120);
        });
        pageSize.addEventListener("change", () => { currentPage = 1; render(); });
        previous.addEventListener("click", () => { if (currentPage > 1) { currentPage -= 1; render(); } });
        next.addEventListener("click", () => { currentPage += 1; render(); });
        render();
      });
    }

    initStandardTables();

    // ===== TABLE demo compatibility renderer =====
    const tableBody = document.getElementById("tableBody");
    const tableInfo = document.getElementById("tableInfo");
    const paginationInfo = document.getElementById("paginationInfo");
    const pageText = document.getElementById("pageText");
    const prevBtn = document.getElementById("prevPage");
    const nextBtn = document.getElementById("nextPage");
    const searchInput = document.getElementById("tableSearch");
    const roleFilter = document.getElementById("tableRoleFilter");
    const statusFilter = document.getElementById("tableStatusFilter");
    const resetFilterBtn = document.getElementById("tableResetFilter");

    if (
      !tableBody ||
      !tableInfo ||
      !pageText ||
      !prevBtn ||
      !nextBtn ||
      !searchInput
    ) {
      return;
    }

    const tableData = [
      {
        name: "Budi Santoso",
        email: "budi@email.com",
        role: "Admin",
        roleClass: "primary",
        status: "Active",
        statusClass: "success",
        date: "19 Jun 2026",
        color: "linear-gradient(135deg,#635bff,#00b8ff)",
      },
      {
        name: "Siti Rahma",
        email: "siti@email.com",
        role: "Editor",
        roleClass: "warning",
        status: "Active",
        statusClass: "success",
        date: "18 Jun 2026",
        color: "linear-gradient(135deg,#ff7a59,#f59e0b)",
      },
      {
        name: "Andi Wijaya",
        email: "andi@email.com",
        role: "Member",
        roleClass: "primary",
        status: "Pending",
        statusClass: "warning",
        date: "17 Jun 2026",
        color: "linear-gradient(135deg,#6d28d9,#635bff)",
      },
      {
        name: "Dewi Kartika",
        email: "dewi@email.com",
        role: "Editor",
        roleClass: "warning",
        status: "Active",
        statusClass: "success",
        date: "16 Jun 2026",
        color: "linear-gradient(135deg,#16a34a,#22d3ee)",
      },
      {
        name: "Rizky Hakim",
        email: "rizky@email.com",
        role: "Member",
        roleClass: "primary",
        status: "Inactive",
        statusClass: "danger",
        date: "15 Jun 2026",
        color: "linear-gradient(135deg,#ef4444,#ff7a59)",
      },
      {
        name: "Maya Sari",
        email: "maya@email.com",
        role: "Admin",
        roleClass: "primary",
        status: "Active",
        statusClass: "success",
        date: "14 Jun 2026",
        color: "linear-gradient(135deg,#635bff,#00b8ff)",
      },
      {
        name: "Fajar Pratama",
        email: "fajar@email.com",
        role: "Member",
        roleClass: "primary",
        status: "Active",
        statusClass: "success",
        date: "13 Jun 2026",
        color: "linear-gradient(135deg,#ff7a59,#f59e0b)",
      },
      {
        name: "Lina Marlina",
        email: "lina@email.com",
        role: "Editor",
        roleClass: "warning",
        status: "Pending",
        statusClass: "warning",
        date: "12 Jun 2026",
        color: "linear-gradient(135deg,#6d28d9,#635bff)",
      },
      {
        name: "Hendra Gunawan",
        email: "hendra@email.com",
        role: "Member",
        roleClass: "primary",
        status: "Active",
        statusClass: "success",
        date: "11 Jun 2026",
        color: "linear-gradient(135deg,#16a34a,#22d3ee)",
      },
      {
        name: "Rina Wulandari",
        email: "rina@email.com",
        role: "Editor",
        roleClass: "warning",
        status: "Inactive",
        statusClass: "danger",
        date: "10 Jun 2026",
        color: "linear-gradient(135deg,#ef4444,#ff7a59)",
      },
      {
        name: "Agus Setiawan",
        email: "agus@email.com",
        role: "Admin",
        roleClass: "primary",
        status: "Active",
        statusClass: "success",
        date: "09 Jun 2026",
        color: "linear-gradient(135deg,#635bff,#00b8ff)",
      },
      {
        name: "Putri Ayu",
        email: "putri@email.com",
        role: "Member",
        roleClass: "primary",
        status: "Active",
        statusClass: "success",
        date: "08 Jun 2026",
        color: "linear-gradient(135deg,#ff7a59,#f59e0b)",
      },
      {
        name: "Bambang Suryadi",
        email: "bambang@email.com",
        role: "Editor",
        roleClass: "warning",
        status: "Pending",
        statusClass: "warning",
        date: "07 Jun 2026",
        color: "linear-gradient(135deg,#6d28d9,#635bff)",
      },
      {
        name: "Nina Kurniawati",
        email: "nina@email.com",
        role: "Member",
        roleClass: "primary",
        status: "Active",
        statusClass: "success",
        date: "06 Jun 2026",
        color: "linear-gradient(135deg,#16a34a,#22d3ee)",
      },
      {
        name: "Joko Widodo",
        email: "joko@email.com",
        role: "Admin",
        roleClass: "primary",
        status: "Active",
        statusClass: "success",
        date: "05 Jun 2026",
        color: "linear-gradient(135deg,#ef4444,#ff7a59)",
      },
      {
        name: "Sri Mulyani",
        email: "sri@email.com",
        role: "Editor",
        roleClass: "warning",
        status: "Active",
        statusClass: "success",
        date: "04 Jun 2026",
        color: "linear-gradient(135deg,#635bff,#00b8ff)",
      },
      {
        name: "Budi Darma",
        email: "budi.darma@email.com",
        role: "Member",
        roleClass: "primary",
        status: "Inactive",
        statusClass: "danger",
        date: "03 Jun 2026",
        color: "linear-gradient(135deg,#ff7a59,#f59e0b)",
      },
      {
        name: "Ratna Dewi",
        email: "ratna@email.com",
        role: "Editor",
        roleClass: "warning",
        status: "Active",
        statusClass: "success",
        date: "02 Jun 2026",
        color: "linear-gradient(135deg,#6d28d9,#635bff)",
      },
      {
        name: "Tono Supriyadi",
        email: "tono@email.com",
        role: "Member",
        roleClass: "primary",
        status: "Pending",
        statusClass: "warning",
        date: "01 Jun 2026",
        color: "linear-gradient(135deg,#16a34a,#22d3ee)",
      },
      {
        name: "Yuni Astuti",
        email: "yuni@email.com",
        role: "Admin",
        roleClass: "primary",
        status: "Active",
        statusClass: "success",
        date: "31 May 2026",
        color: "linear-gradient(135deg,#ef4444,#ff7a59)",
      },
      {
        name: "Dedi Corbuzier",
        email: "dedi@email.com",
        role: "Member",
        roleClass: "primary",
        status: "Active",
        statusClass: "success",
        date: "30 May 2026",
        color: "linear-gradient(135deg,#635bff,#00b8ff)",
      },
      {
        name: "Siska Annisa",
        email: "siska@email.com",
        role: "Editor",
        roleClass: "warning",
        status: "Active",
        statusClass: "success",
        date: "29 May 2026",
        color: "linear-gradient(135deg,#ff7a59,#f59e0b)",
      },
      {
        name: "Rudi Hermawan",
        email: "rudi@email.com",
        role: "Member",
        roleClass: "primary",
        status: "Inactive",
        statusClass: "danger",
        date: "28 May 2026",
        color: "linear-gradient(135deg,#6d28d9,#635bff)",
      },
      {
        name: "Wati Sulastri",
        email: "wati@email.com",
        role: "Editor",
        roleClass: "warning",
        status: "Active",
        statusClass: "success",
        date: "27 May 2026",
        color: "linear-gradient(135deg,#16a34a,#22d3ee)",
      },
      {
        name: "Herman Yusuf",
        email: "herman@email.com",
        role: "Admin",
        roleClass: "primary",
        status: "Active",
        statusClass: "success",
        date: "26 May 2026",
        color: "linear-gradient(135deg,#ef4444,#ff7a59)",
      },
    ];

    const PER_PAGE = 10;
    let currentPage = 1;
    let filteredData = [...tableData];

    function getFilteredData() {
      const q = searchInput.value.toLowerCase().trim();
      const role = roleFilter?.value || "";
      const status = statusFilter?.value || "";

      return tableData.filter((item) => {
        const matchesQuery =
          item.name.toLowerCase().includes(q) ||
          item.email.toLowerCase().includes(q) ||
          item.role.toLowerCase().includes(q) ||
          item.status.toLowerCase().includes(q) ||
          item.date.toLowerCase().includes(q);

        const matchesRole = !role || item.role === role;
        const matchesStatus = !status || item.status === status;

        return matchesQuery && matchesRole && matchesStatus;
      });
    }

    function renderTable() {
      const totalPages = Math.max(1, Math.ceil(filteredData.length / PER_PAGE));
      if (currentPage > totalPages) currentPage = totalPages;

      const start = (currentPage - 1) * PER_PAGE;
      const end = start + PER_PAGE;
      const pageData = filteredData.slice(start, end);

      if (pageData.length === 0) {
        tableBody.innerHTML =
          '<tr><td colspan="5" style="text-align:center;padding:40px 20px;color:var(--rt-muted);font-weight:700">No items found</td></tr>';
      } else {
        tableBody.innerHTML = pageData
          .map((item) => {
            const initials = item.name
              .split(" ")
              .map((n) => n[0])
              .slice(0, 2)
              .join("");
            return `
                    <tr>
                        <td>
                            <div class="rt-table-user">
                                <div class="rt-table-avatar" style="background:${item.color}">${initials}</div>
                                <div>
                                    <div class="rt-table-name">${item.name}</div>
                                    <div class="rt-table-email">${item.email}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="rt-badge rt-badge--${item.roleClass}"><span class="rt-badge-dot"></span>${item.role}</span></td>
                        <td><span class="rt-badge rt-badge--${item.statusClass}"><span class="rt-badge-dot"></span>${item.status}</span></td>
                        <td>${item.date}</td>
                        <td>
                            <div style="display:flex;gap:4px">
                                <button class="rt-btn rt-anishow rt-anishow rt-btn-ghost rt-btn-sm rt-btn-icon" title="Edit">
                                    <i class="rt rt-edit" aria-hidden="true"></i>
                                </button>
                                <button class="rt-btn rt-anishow rt-anishow rt-btn-ghost rt-btn-sm rt-btn-icon" title="Delete" style="color:var(--rt-danger)">
                                    <i class="rt rt-delete" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
          })
          .join("");
      }

      const showing = filteredData.length === 0 ? 0 : start + 1;
      const showingEnd = Math.min(end, filteredData.length);
      tableInfo.innerHTML = `Showing <strong>${showing}-${showingEnd}</strong> of <strong>${filteredData.length}</strong> items`;
      if (paginationInfo) {
        paginationInfo.textContent = `Page ${currentPage} / ${totalPages}`;
      }
      pageText.textContent = `Page ${currentPage} / ${totalPages}`;
      prevBtn.disabled = currentPage <= 1;
      nextBtn.disabled = currentPage >= totalPages;
    }

    prevBtn.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage--;
        renderTable();
      }
    });

    nextBtn.addEventListener("click", () => {
      const totalPages = Math.ceil(filteredData.length / PER_PAGE);
      if (currentPage < totalPages) {
        currentPage++;
        renderTable();
      }
    });

    function applyFilters() {
      filteredData = getFilteredData();
      currentPage = 1;
      renderTable();
    }

    searchInput.addEventListener("input", applyFilters);
    roleFilter?.addEventListener("change", applyFilters);
    statusFilter?.addEventListener("change", applyFilters);

    resetFilterBtn?.addEventListener("click", () => {
      searchInput.value = "";
      if (roleFilter) roleFilter.value = "";
      if (statusFilter) statusFilter.value = "";
      applyFilters();
    });

    filteredData = getFilteredData();
    renderTable();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initRetroTerm, {
      once: true,
    });
  } else {
    initRetroTerm();
  }
})();
