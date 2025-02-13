import React from "react";
import { useCart } from "../context/CartContext"; // Use custom hook instead of CartContext
import { useNavigate, useLocation } from "react-router-dom";
import "./Header.css";

const Header = ({ toggleOverlay }) => {
  const { cart } = useCart(); // Now using the custom hook to access cart data
  const itemCount = cart.reduce((total, item) => total + item.quantity, 0);
  const navigate = useNavigate();
  const location = useLocation();

  // Extract active category from URL query parameters; default to "all"
  const queryParams = new URLSearchParams(location.search);
  const activeCategory = queryParams.get("category") || "all";

  const handleCategoryClick = (category) => {
    navigate(`/products?category=${category}`);
  };

  // Returns the proper data-testid and class based on active category
  const getCategoryButtonProps = (category) => {
    return category === activeCategory
      ? { "data-testid": "active-category-link", className: "active" }
      : { "data-testid": "category-link" };
  };

  return (
    <header className="header">
      <nav>
        <button
          {...getCategoryButtonProps("all")}
          onClick={() => handleCategoryClick("all")}
        >
          All
        </button>
        <button
          {...getCategoryButtonProps("tech")}
          onClick={() => handleCategoryClick("tech")}
        >
          Tech
        </button>
        <button
          {...getCategoryButtonProps("clothes")}
          onClick={() => handleCategoryClick("clothes")}
        >
          Clothes
        </button>
      </nav>
      <button data-testid="cart-btn" onClick={toggleOverlay}>
        Cart{" "}
        {itemCount > 0 && (
          <span>
            ({itemCount} {itemCount === 1 ? "Item" : "Items"})
          </span>
        )}
      </button>
    </header>
  );
};

export default Header;
