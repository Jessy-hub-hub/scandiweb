import React from "react";
import { gql, useQuery } from "@apollo/client";
import { useLocation, useNavigate } from "react-router-dom";
import { useCart } from "../context/CartContext";
import "./ProductListingPage.css";

const GET_CATEGORIES_AND_PRODUCTS = gql`
  query GetCategoriesAndProducts {
    products {
      id
      name
      inStock
      gallery
      prices {
        amount
        currency {
          symbol
        }
      }
      category
      attributes {
        id
        name
        type
        items {
          id
          displayValue
          value
        }
      }
    }
  }
`;

const ProductListingPage = () => {
  const { loading, error, data } = useQuery(GET_CATEGORIES_AND_PRODUCTS);
  const location = useLocation();
  const navigate = useNavigate();
  const { addToCart } = useCart();

  // Extract the first non-empty segment from the URL; default to "all"
  const segments = location.pathname.split("/").filter(Boolean);
  const selectedCategory = segments[0] || "all";

  if (loading) return <p>Loading...</p>;
  if (error) return <p>Error: {error.message}</p>;

  const filteredProducts =
    selectedCategory === "all"
      ? data.products
      : data.products.filter(
          (product) => product.category === selectedCategory
        );

  const handleQuickShop = (e, product) => {
    e.stopPropagation();
    const defaultOptions =
      product.attributes?.reduce((acc, attribute) => {
        if (attribute.items && attribute.items.length > 0) {
          acc[attribute.id] = attribute.items[0].value;
        }
        return acc;
      }, {}) || {};

    const productWithPrice = { ...product, price: product.prices[0] };
    addToCart(productWithPrice, defaultOptions);
    alert(`Quick Shop: Added ${product.name} to cart with default options!`);
  };

  const handleProductClick = (productId) => {
    navigate(`/product/${productId}`);
  };

  return (
    <div className="product-listing-page">
      {/* Display the active category as a title (static text) */}
      <h1 className="category-title">
        {selectedCategory.charAt(0).toUpperCase() + selectedCategory.slice(1)}
      </h1>

      {/* Note: Removed the clickable category navigation from here */}

      <div className="product-grid">
        {filteredProducts.map((product) => (
          <div
            key={product.id}
            className={`product-card ${product.inStock ? "" : "out-of-stock"}`}
            onClick={() => handleProductClick(product.id)}
          >
            <div className="image-container">
              <img src={product.gallery[0]} alt={product.name} />
              {!product.inStock && (
                <div className="out-of-stock-overlay">Out of Stock</div>
              )}
            </div>
            <div className="product-details">
              <p className="product-name">{product.name}</p>
              <p className="product-price">
                {product.prices[0]?.currency.symbol}
                {product.prices[0]?.amount.toFixed(2)}
              </p>
            </div>
            {product.inStock && (
              <button
                className="quick-shop"
                onClick={(e) => handleQuickShop(e, product)}
              >
                🛒
              </button>
            )}
          </div>
        ))}
      </div>
    </div>
  );
};

export default ProductListingPage;
