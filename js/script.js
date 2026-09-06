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


const PRODUCT_DATA = [
  {
    name: "Velvet Bloom",
    cat: "Floral",
    desc: "Peony, rose absolute, soft musk.",
    price: "₱3,500",
    rating: 5,
    img: "images/products/product-1.jpg"
  },

  {
    name: "Cedar & Smoke",
    cat: "Woody",
    desc: "Cedarwood, vetiver, black amber.",
    price: "₱3,000",
    rating: 4,
    img: "images/products/product-2.jpg"
  },

  {
    name: "Linen Air",
    cat: "Fresh",
    desc: "Sea salt, bergamot, white musk.",
    price: "₱2,200",
    rating: 5,
    img: "images/products/product-3.jpg"
  },

  {
    name: "Amber Dusk",
    cat: "Oriental",
    desc: "Amber resin, saffron, tonka bean.",
    price: "₱2,000",
    rating: 5,
    img: "images/products/product-4.jpg"
  },

  {
    name: "Citrus Grove",
    cat: "Citrus",
    desc: "Blood orange, neroli, green tea.",
    price: "₱2,600",
    rating: 4,
    img: "images/products/product-5.jpg"
  },

  {
    name: "Vanilla Noir",
    cat: "Vanilla",
    desc: "Madagascar vanilla, oak, dark musk.",
    price: "₱4,300",
    rating: 5,
    img: "images/products/product-6.jpg"
  }
];


function renderProducts() {
  const grid = document.getElementById("productGrid");

  grid.innerHTML = PRODUCT_DATA.map(p => `
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

          <button class="btn btn-dark-outline">
            View Details
          </button>

          <button class="btn btn-primary">
            Add to Cart
          </button>

        </div>

      </div>

    </article>
  `).join("");
}


const COLLECTION_DATA = [
  {
    name: "Signature Collection",
    desc: "The scents that started it all.",
    img: "images/collection/collection-1.jpg"
  },

  {
    name: "Luxury Collection",
    desc: "Rare ingredients, limited pours.",
    img: "images/collection/collection-2.jpg"
  },

  {
    name: "Everyday Collection",
    desc: "Light, wearable, all day scent.",
    img: "images/collection/collection-3.jpg"
  },

  {
    name: "Limited Edition",
    desc: "Seasonal blends, once available.",
    img: "images/collection/collection-4.jpg"
  }
];


function renderCollections() {
  const grid = document.getElementById("collectionsGrid");

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

        <a>
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


const contactForm = document.getElementById("contactForm");
const formNote = document.getElementById("formNote");


contactForm.addEventListener("submit", function (e) {
  e.preventDefault();

  formNote.textContent =
    "Thank you your message has been noted. We'll reply within 1 business day.";

  contactForm.reset();
});


document.getElementById("year").textContent =
  new Date().getFullYear();


renderProducts();
renderCollections();
renderReviews();