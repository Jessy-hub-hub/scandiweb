import React from "react";
import { Link, useLocation } from "react-router-dom";
import { useCart } from "../context/CartContext";
import "./Header.css";

const Header = ({ toggleOverlay }) => {
  const { cart } = useCart();
  const location = useLocation();
  const itemCount = cart.reduce((total, item) => total + item.quantity, 0);

  // Determine the active category.
  // When at "/" we treat it as "all", otherwise remove the leading slash.
  const activeCategory = location.pathname === "/" ? "all" : location.pathname.slice(1);

  const getCategoryLinkProps = (category) =>
    category === activeCategory
      ? { "data-testid": "active-category-link", className: "active" }
      : { "data-testid": "category-link" };

  return (
    <header className="header">
      <nav>
        <Link to="/all" {...getCategoryLinkProps("all")}>
          All
        </Link>
        <Link to="/tech" {...getCategoryLinkProps("tech")}>
          Tech
        </Link>
        <Link to="/clothes" {...getCategoryLinkProps("clothes")}>
          Clothes
        </Link>
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
