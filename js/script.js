function starSVG(filled) {
  return `
    <svg
      viewBox="0 0 24 24"
      fill="${filled ? 'currentColor' : 'none'}"
      stroke="currentColor"
      stroke-width="1.4"
    >
      <path d="M12 2l3.1 6.6 7.2.8-5.4 4.9 1.5 7.2L12 17.9 5.6 21.5l1.5-7.2L1.7 9.4l7.2-.8L12 2z"/>
    </svg>
  `;
}


// Shop products now live in the database (see database/empenado_db.sql,
// table `products`) and are managed from the admin dashboard. They're
// fetched from get-products.php the same way Special Collections are
// fetched from get-collections.php below. This array starts empty and
// is filled in by loadProducts(); everything that reads it keeps
// working the same way once the data arrives.
let PRODUCT_DATA = [];

async function loadProducts() {
  try {
    const res = await fetch("get-products.php");

    if (!res.ok) {
      throw new Error("Request failed with status " + res.status);
    }

    const data = await res.json();

    if (!Array.isArray(data)) {
      throw new Error("Unexpected response from get-products.php");
    }

    PRODUCT_DATA = data;
  } catch (err) {
    console.error("Failed to load shop products from the database:", err);
    const grid = document.getElementById("productGrid");
    if (grid) {
      grid.innerHTML = `<p class="form-alert form-alert-error">Couldn't load products right now. Please try again later.</p>`;
    }
    return;
  }

  renderProducts();
}


function renderProducts(list = PRODUCT_DATA) {
  const grid = document.getElementById("productGrid");
  const noResults = document.getElementById("noResults");

  if (noResults) {
    noResults.hidden = list.length !== 0;
  }

  grid.innerHTML = list.map(p => `
    <article class="product-card">

      <div class="product-media">

        <span class="product-tag">${p.cat}</span>

        <button
          class="product-wish"
          aria-label="Save ${p.name} to wishlist"
        >
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.6"
          >
            <path d="M12 21s-7.5-4.8-10-9.3C.5 8 2 4 6 4c2.2 0 3.7 1.2 4.5 2.4C11.3 5.2 12.8 4 15 4c4 0 5.5 4 4 7.7C19.5 16.2 12 21 12 21z"/>
          </svg>
        </button>

        <img
          src="${p.img}"
          alt="${p.name} bottle"
        >

      </div>


      <div class="product-info">

        <span class="product-cat">
          ${p.cat} Fragrance
        </span>

        <h3>${p.name}</h3>

        <p class="desc">
          ${p.desc}
        </p>

        <div class="rating">
          ${Array.from(
            { length: 5 },
            (_, i) => starSVG(i < p.rating)
          ).join("")}

          <span>${p.rating}.0</span>
        </div>

        <div class="price-row">
          <span class="price">
            ${p.price}
          </span>
        </div>

        <div class="product-actions">

          <button class="btn btn-dark-outline" data-view-details="${p.id}">
            View Details
          </button>

          <button class="btn btn-primary" data-add-to-cart="${p.id}">
            Add to Cart
          </button>

        </div>

      </div>

    </article>
  `).join("");
}


// Special Collections now live in the database (see database/empenado_db.sql,
// table `collections`) and are fetched from get-collections.php. This array
// starts empty and is filled in by loadCollections() below; everything that
// reads it (renderCollections, findProductById, search, cart) keeps working
// the same way once the data arrives.
let COLLECTION_DATA = [];

async function loadCollections() {
  const grid = document.getElementById("collectionsGrid");

  try {
    const res = await fetch("get-collections.php");

    if (!res.ok) {
      throw new Error("Request failed with status " + res.status);
    }

    const data = await res.json();

    if (!Array.isArray(data)) {
      throw new Error("Unexpected response from get-collections.php");
    }

    COLLECTION_DATA = data;
  } catch (err) {
    console.error("Failed to load Special Collections from the database:", err);
    if (grid) {
      grid.innerHTML = `<p class="form-alert form-alert-error">Couldn't load Special Collections right now. Please try again later.</p>`;
    }
    return;
  }

  renderCollections();
}


function renderCollections() {
  const grid = document.getElementById("collectionsGrid");
  if (!grid) return;

  grid.innerHTML = COLLECTION_DATA.map(c => `
    <div class="collection-card">

      <div class="bg">
        <img
          src="${c.img}"
          alt="${c.name}"
        >
      </div>

      <div class="collection-body">

        <span class="eyebrow">
        </span>

        <h3>${c.name}</h3>

        <p>
          ${c.desc}
        </p>

        <a href="#" data-view-details="${c.product.id}">
          View Details
        </a>

      </div>

    </div>
  `).join("");
}


const REVIEW_DATA = [
  {
    name: "Amara S.",
    loc: "Dauin",
    rating: 5,
    text: "Cedar & Smoke lasted all day and got me three compliments before lunch. Worth every peso."
  },

  {
    name: "Julien R.",
    loc: "Dumaguete City",
    rating: 5,
    text: "The bottle alone feels like a gift. Amber Dusk is warm without being heavy exactly what I wanted."
  },

  {
    name: "Mei L.",
    loc: "Valencia",
    rating: 4,
    text: "Linen Air is my new everyday scent. Clean, fresh, not overpowering in the humidity here."
  }
];


function renderReviews() {
  const grid = document.getElementById("reviewsGrid");

  grid.innerHTML = REVIEW_DATA.map(r => `
    <div class="review-card">

      <div class="review-stars">
        ${Array.from(
          { length: 5 },
          (_, i) => starSVG(i < r.rating)
        ).join("")}
      </div>

      <p>
        "${r.text}"
      </p>

      <div class="reviewer">

        <div class="avatar">
          ${r.name.charAt(0)}
        </div>

        <div>

          <div class="reviewer-name">
            ${r.name}
          </div>

          <div class="reviewer-loc">
            ${r.loc}
          </div>

        </div>

      </div>

    </div>
  `).join("");
}


const header = document.getElementById("siteHeader");


function handleScroll() {
  if (window.scrollY > 60) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
}


window.addEventListener("scroll", handleScroll);


const navToggle = document.getElementById("navToggle");
const navLinksEl = document.querySelector(".nav-links");


navToggle.addEventListener("click", () => {
  const isOpen = navLinksEl.style.display === "flex";

  navLinksEl.style.display = isOpen ? "none" : "flex";

  navLinksEl.style.cssText += isOpen
    ? ""
    : `
        flex-direction: column;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: rgba(15, 23, 32, 0.98);
        padding: 24px;
        gap: 20px;
        text-align: center;
      `;

  navToggle.setAttribute(
    "aria-expanded",
    String(!isOpen)
  );
});


// The contact form now submits for real to contact-function.php,
// which validates the input, saves it to the `messages` table,
// and redirects back here with a status message shown above.


document.getElementById("year").textContent =
  new Date().getFullYear();


/* =========================
   PRODUCT SEARCH
========================= */

function getAllSearchableProducts() {
  const collectionProducts = COLLECTION_DATA.map(c => c.product);
  return PRODUCT_DATA.concat(collectionProducts);
}

function searchProducts(term) {
  const query = term.trim().toLowerCase();
  const allProducts = getAllSearchableProducts();

  if (query === "") {
    return allProducts;
  }

  return allProducts.filter(p =>
    p.name.toLowerCase().includes(query) ||
    p.cat.toLowerCase().includes(query) ||
    p.desc.toLowerCase().includes(query)
  );
}

/* =========================
   SEARCH POPUP
========================= */

const searchToggle = document.getElementById("searchToggle");
const searchModal = document.getElementById("searchModal");
const searchModalBackdrop = document.getElementById("searchModalBackdrop");
const searchModalClose = document.getElementById("searchModalClose");
const searchModalResults = document.getElementById("searchModalResults");
const popupSearchInput = document.getElementById("popupSearchInput");

function renderSearchResults(query) {
  if (!searchModalResults) return;

  const trimmed = query.trim();

  if (trimmed === "") {
    searchModalResults.innerHTML =
      '<p class="search-modal-hint">Start typing to search our collection.</p>';
    return;
  }

  const results = searchProducts(trimmed);

  if (results.length === 0) {
    searchModalResults.innerHTML =
      `<p class="search-modal-empty">No perfumes match "${trimmed}".</p>`;
    return;
  }

  const countLabel =
    `<p class="search-modal-count">${results.length} result${results.length === 1 ? "" : "s"}</p>`;

  const items = results.map(p => `
    <button type="button" class="search-result-item" data-product-id="${p.id}">
      <img src="${p.img}" alt="${p.name} bottle">
      <div class="search-result-info">
        <div class="name">${p.name}</div>
        <div class="meta">${p.cat} Fragrance</div>
      </div>
      <span class="search-result-price">${p.price}</span>
    </button>
  `).join("");

  searchModalResults.innerHTML = countLabel + items;
}

function openSearchModal() {
  if (!searchModal) return;
  searchModal.classList.add("is-open");
  searchModal.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden";
  renderSearchResults(popupSearchInput ? popupSearchInput.value : "");
  setTimeout(() => popupSearchInput && popupSearchInput.focus(), 150);
}

function closeSearchModal() {
  if (!searchModal) return;
  searchModal.classList.remove("is-open");
  searchModal.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";
}

if (searchToggle) {
  searchToggle.addEventListener("click", (e) => {
    e.preventDefault();
    openSearchModal();
  });
}

if (searchModalClose) {
  searchModalClose.addEventListener("click", closeSearchModal);
}

if (searchModalBackdrop) {
  searchModalBackdrop.addEventListener("click", closeSearchModal);
}

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && searchModal && searchModal.classList.contains("is-open")) {
    closeSearchModal();
  }
});

if (popupSearchInput) {
  popupSearchInput.addEventListener("input", () => {
    renderSearchResults(popupSearchInput.value);
  });
}

function highlightProductCard(productId) {
  const grid = document.getElementById("productGrid");
  if (!grid) return;

  const card = grid.querySelector(`[data-add-to-cart="${productId}"]`)?.closest(".product-card");
  if (!card) return;

  card.classList.add("is-highlighted");
  setTimeout(() => card.classList.remove("is-highlighted"), 1600);
}

if (searchModalResults) {
  searchModalResults.addEventListener("click", (e) => {
    const item = e.target.closest(".search-result-item");
    if (!item) return;

    const productId = item.dataset.productId;

    closeSearchModal();

    // Restore the full, original product arrangement (not a filtered list)
    renderProducts();

    const isRegularProduct = PRODUCT_DATA.some(p => p.id === productId);

    if (isRegularProduct) {
      // Regular shop products: scroll to the shop grid and highlight the card
      setTimeout(() => {
        document.getElementById("shop").scrollIntoView({ behavior: "smooth" });
        highlightProductCard(productId);
      }, 100);
    } else {
      // Collection products aren't in the shop grid, so open their details
      // modal directly the same way "View Details" on a collection card does
      openDetailsModal(productId);
    }
  });
}


/* =========================
   CART
========================= */

const CART_STORAGE_KEY = "empenado_cart";

function loadCart() {
  try {
    const raw = localStorage.getItem(CART_STORAGE_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch (err) {
    return [];
  }
}

let CART = loadCart();

function saveCart() {
  localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(CART));
}

function parsePrice(priceStr) {
  return Number(String(priceStr).replace(/[^0-9.]/g, "")) || 0;
}

function formatPeso(amount) {
  return "₱" + amount.toLocaleString("en-PH");
}

function findProductById(id) {
  return (
    PRODUCT_DATA.find(p => p.id === id) ||
    COLLECTION_DATA.map(c => c.product).find(p => p.id === id)
  );
}

function addToCart(productId, qty = 1) {
  const product = findProductById(productId);
  if (!product) return;

  const existing = CART.find(item => item.id === productId);

  if (existing) {
    existing.qty += qty;
  } else {
    CART.push({
      id: product.id,
      name: product.name,
      price: product.price,
      img: product.img,
      qty
    });
  }

  saveCart();
  updateCartUI();
  showToast(`${product.name} added to cart`);
}

function removeFromCart(productId) {
  CART = CART.filter(item => item.id !== productId);
  saveCart();
  updateCartUI();
}

function changeQty(productId, delta) {
  const item = CART.find(i => i.id === productId);
  if (!item) return;

  item.qty += delta;

  if (item.qty <= 0) {
    removeFromCart(productId);
    return;
  }

  saveCart();
  updateCartUI();
}

function cartCount() {
  return CART.reduce((sum, item) => sum + item.qty, 0);
}

function cartTotal() {
  return CART.reduce((sum, item) => sum + parsePrice(item.price) * item.qty, 0);
}

const cartToggle = document.getElementById("cartToggle");
const cartBadge = document.getElementById("cartBadge");
const cartModal = document.getElementById("cartModal");
const cartModalBackdrop = document.getElementById("cartModalBackdrop");
const cartModalClose = document.getElementById("cartModalClose");
const cartItemsEl = document.getElementById("cartItems");
const cartTotalEl = document.getElementById("cartTotal");
const cartCheckoutBtn = document.getElementById("cartCheckout");

function renderCartItems() {
  if (!cartItemsEl) return;

  if (CART.length === 0) {
    cartItemsEl.innerHTML = '<p class="cart-empty">Your cart is empty.</p>';
    return;
  }

  cartItemsEl.innerHTML = CART.map(item => `
    <div class="cart-item">
      <img src="${item.img}" alt="${item.name} bottle">
      <div class="cart-item-info">
        <div class="name">${item.name}</div>
        <div class="price">${item.price} x ${item.qty}</div>
      </div>
      <div class="cart-item-qty">
        <button type="button" class="cart-qty-btn" data-qty-decrease="${item.id}" aria-label="Decrease quantity">-</button>
        <span>${item.qty}</span>
        <button type="button" class="cart-qty-btn" data-qty-increase="${item.id}" aria-label="Increase quantity">+</button>
      </div>
      <button type="button" class="cart-item-remove" data-remove-item="${item.id}">Remove</button>
    </div>
  `).join("");
}

function updateCartUI() {
  const count = cartCount();

  if (cartBadge) {
    cartBadge.textContent = String(count);
    cartBadge.hidden = count === 0;
  }

  if (cartToggle) {
    cartToggle.setAttribute("aria-label", `Cart, ${count} item${count === 1 ? "" : "s"}`);
  }

  if (cartTotalEl) {
    cartTotalEl.textContent = formatPeso(cartTotal());
  }

  renderCartItems();
}

let toastTimer = null;

function showToast(message) {
  const toast = document.getElementById("toast");
  if (!toast) return;

  toast.textContent = message;
  toast.classList.add("is-visible");

  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toast.classList.remove("is-visible");
  }, 2200);
}

function openCartModal() {
  if (!cartModal) return;
  cartModal.classList.add("is-open");
  cartModal.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden";
  updateCartUI();
}

function closeCartModal() {
  if (!cartModal) return;
  cartModal.classList.remove("is-open");
  cartModal.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";
}

if (cartToggle) {
  cartToggle.addEventListener("click", (e) => {
    e.preventDefault();
    openCartModal();
  });
}

if (cartModalClose) {
  cartModalClose.addEventListener("click", closeCartModal);
}

if (cartModalBackdrop) {
  cartModalBackdrop.addEventListener("click", closeCartModal);
}

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && cartModal && cartModal.classList.contains("is-open")) {
    closeCartModal();
  }
});

if (cartItemsEl) {
  cartItemsEl.addEventListener("click", (e) => {
    const increaseBtn = e.target.closest("[data-qty-increase]");
    const decreaseBtn = e.target.closest("[data-qty-decrease]");
    const removeBtn = e.target.closest("[data-remove-item]");

    if (increaseBtn) {
      changeQty(increaseBtn.dataset.qtyIncrease, 1);
    } else if (decreaseBtn) {
      changeQty(decreaseBtn.dataset.qtyDecrease, -1);
    } else if (removeBtn) {
      removeFromCart(removeBtn.dataset.removeItem);
    }
  });
}

if (cartCheckoutBtn) {
  cartCheckoutBtn.addEventListener("click", async () => {
    if (CART.length === 0) {
      showToast("Your cart is empty");
      return;
    }

    const originalText = cartCheckoutBtn.textContent;
    cartCheckoutBtn.disabled = true;
    cartCheckoutBtn.textContent = "Placing order...";

    try {
      const res = await fetch("checkout-function.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ items: CART, total: cartTotal() })
      });

      const data = await res.json();

      if (res.status === 401) {
        // Not logged in — send them to log in, then they can come back and checkout.
        showToast(data.message || "Please log in to check out");
        window.location.href = data.redirect || "login.php";
        return;
      }

      if (!res.ok || !data.success) {
        showToast(data.message || "Checkout failed. Please try again.");
        return;
      }

      // Order saved to the database — clear the local cart.
      CART = [];
      saveCart();
      updateCartUI();
      closeCartModal();
      showToast(data.message || "Order placed!");
    } catch (err) {
      console.error("Checkout failed:", err);
      showToast("Checkout failed. Please check your connection.");
    } finally {
      cartCheckoutBtn.disabled = false;
      cartCheckoutBtn.textContent = originalText;
    }
  });
}

/* =========================
   AUTH REQUIRED MODAL
   Guests must log in / register before adding to cart
========================= */
const authModal = document.getElementById("authModal");
const authModalBackdrop = document.getElementById("authModalBackdrop");
const authModalClose = document.getElementById("authModalClose");

function openAuthModal() {
  if (!authModal) return;
  authModal.classList.add("is-open");
  authModal.setAttribute("aria-hidden", "false");
}

function closeAuthModal() {
  if (!authModal) return;
  authModal.classList.remove("is-open");
  authModal.setAttribute("aria-hidden", "true");
}

if (authModalClose) {
  authModalClose.addEventListener("click", closeAuthModal);
}

if (authModalBackdrop) {
  authModalBackdrop.addEventListener("click", closeAuthModal);
}

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && authModal && authModal.classList.contains("is-open")) {
    closeAuthModal();
  }
});

// Returns true if the guest is allowed to proceed (i.e. is logged in).
// Otherwise opens the login/register prompt and returns false.
function requireLogin() {
  if (window.IS_LOGGED_IN) return true;
  openAuthModal();
  return false;
}

// Event delegation so "Add to Cart" works after every re-render (search, etc.)
const productGridEl = document.getElementById("productGrid");
if (productGridEl) {
  productGridEl.addEventListener("click", (e) => {
    const addBtn = e.target.closest("[data-add-to-cart]");
    const viewBtn = e.target.closest("[data-view-details]");

    if (addBtn) {
      if (!requireLogin()) return;

      addToCart(addBtn.dataset.addToCart);

      const originalText = addBtn.textContent;
      addBtn.textContent = "Added to Cart";
      addBtn.classList.add("is-added");
      addBtn.disabled = true;

      clearTimeout(addBtn._resetTimer);
      addBtn._resetTimer = setTimeout(() => {
        addBtn.textContent = originalText;
        addBtn.classList.remove("is-added");
        addBtn.disabled = false;
      }, 1500);
    } else if (viewBtn) {
      openDetailsModal(viewBtn.dataset.viewDetails);
    }
  });
}

// "View Details" on the Special Collections cards opens the same details modal
const collectionsGridEl = document.getElementById("collectionsGrid");
if (collectionsGridEl) {
  collectionsGridEl.addEventListener("click", (e) => {
    const viewBtn = e.target.closest("[data-view-details]");
    if (!viewBtn) return;

    e.preventDefault();
    openDetailsModal(viewBtn.dataset.viewDetails);
  });
}


/* =========================
   PRODUCT DETAILS MODAL
========================= */

const detailsModal = document.getElementById("detailsModal");
const detailsModalBackdrop = document.getElementById("detailsModalBackdrop");
const detailsModalClose = document.getElementById("detailsModalClose");
const detailsModalBody = document.getElementById("detailsModalBody");

function renderDetailsModal(product) {
  if (!detailsModalBody) return;

  detailsModalBody.innerHTML = `
    <div class="details-modal-media">
      <img src="${product.img}" alt="${product.name} bottle">
    </div>
    <div class="details-modal-info">
      <span class="product-cat">${product.cat} Fragrance</span>
      <h3>${product.name}</h3>
      <div class="rating">
        ${Array.from(
          { length: 5 },
          (_, i) => starSVG(i < product.rating)
        ).join("")}
        <span>${product.rating}.0</span>
      </div>
      <span class="details-size">${product.size} bottle</span>
      <p class="desc">${product.desc}</p>
      <span class="price">${product.price}</span>
      <div class="details-modal-actions">
        <button class="btn btn-primary" data-add-to-cart="${product.id}">
          Add to Cart
        </button>
      </div>
    </div>
  `;
}

function openDetailsModal(productId) {
  const product = findProductById(productId);
  if (!product || !detailsModal) return;

  renderDetailsModal(product);

  detailsModal.classList.add("is-open");
  detailsModal.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden";
}

function closeDetailsModal() {
  if (!detailsModal) return;
  detailsModal.classList.remove("is-open");
  detailsModal.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";
}

if (detailsModalClose) {
  detailsModalClose.addEventListener("click", closeDetailsModal);
}

if (detailsModalBackdrop) {
  detailsModalBackdrop.addEventListener("click", closeDetailsModal);
}

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && detailsModal && detailsModal.classList.contains("is-open")) {
    closeDetailsModal();
  }
});

// Let "Add to Cart" work from inside the details modal too
if (detailsModalBody) {
  detailsModalBody.addEventListener("click", (e) => {
    const addBtn = e.target.closest("[data-add-to-cart]");
    if (!addBtn) return;

    if (!requireLogin()) return;

    addToCart(addBtn.dataset.addToCart);

    const originalText = addBtn.textContent;
    addBtn.textContent = "Added to Cart";
    addBtn.classList.add("is-added");
    addBtn.disabled = true;

    clearTimeout(addBtn._resetTimer);
    addBtn._resetTimer = setTimeout(() => {
      addBtn.textContent = originalText;
      addBtn.classList.remove("is-added");
      addBtn.disabled = false;
    }, 1500);
  });
}


loadProducts();
loadCollections();
renderReviews();
updateCartUI();