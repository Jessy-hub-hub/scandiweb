import React from "react";
import ReactDOM from "react-dom";
import { useCart } from "../context/CartContext";
import "./CartOverlay.css";
import { gql, useMutation } from "@apollo/client";

const CREATE_ORDER = gql`
  mutation CreateOrder($products: [OrderProductInput!]!) {
    createOrder(products: $products) {
      id
      products {
        productId
        quantity
        totalPrice
      }
      totalPrice
    }
  }
`;

const CartOverlay = ({ onClose }) => {
  const { cart, updateQuantity, clearCart, removeFromCart } = useCart();

  const totalQuantity = cart.reduce((sum, item) => sum + item.quantity, 0);
  const totalPrice = cart.reduce(
    (sum, item) => sum + (item.price?.amount || 0) * item.quantity,
    0
  );

  const [createOrder, { loading, error }] = useMutation(CREATE_ORDER, {
    onCompleted: () => {
      clearCart();
      alert("Order placed successfully!");
    },
  });

  const handlePlaceOrder = () => {
    if (cart.length === 0) return;
    const orderProducts = cart.map((item) => ({
      productId: item.id,
      quantity: item.quantity,
      totalPrice: (item.price?.amount || 0) * item.quantity,
    }));
    createOrder({ variables: { products: orderProducts } });
  };

  const handleDecrease = (item) => {
    if (item.quantity === 1) {
      removeFromCart(item);
    } else {
      updateQuantity(item, -1);
    }
  };

  // Build the overlay content
  const overlayContent = (
    <>
      {/* Backdrop for closing the overlay */}
      <div className="backdrop" onClick={onClose} />
      {/* 
          The overlay container has:
          - data-testid="cart-overlay" so tests can find it
          - role="dialog" for accessibility and improved test targeting
          - inline style to force display as flex (visible)
      */}
      <div
        className="cart-overlay"
        data-testid="cart-overlay"
        role="dialog"
        style={{ display: "flex" }}
      >
        <h3>
          My Bag, {totalQuantity} {totalQuantity === 1 ? "Item" : "Items"}
        </h3>
        <div className="cart-items-container">
          {cart.map((item) => (
            // Add data-testid to each cart item container.
            // For example, if item.id is "iphone-12-pro", this will be data-testid="product-iphone-12-pro"
            <div
              key={item.id}
              className="cart-item"
              data-testid={`product-${item.id}`}
            >
              <div className="cart-item-details">
                <p className="cart-item-name">{item.name}</p>
                {item.options &&
                  Object.keys(item.options).map((attrKey) => (
                    <p key={attrKey} className="cart-item-option">
                      {attrKey}: {item.options[attrKey]}
                    </p>
                  ))}
                <p className="cart-item-price">
                  {item.price?.currency?.symbol || "$"}
                  {item.price?.amount || 0}
                </p>
              </div>
              <div className="cart-item-quantity">
                <button onClick={() => handleDecrease(item)}>-</button>
                <span>{item.quantity}</span>
                <button onClick={() => updateQuantity(item, 1)}>+</button>
              </div>
              <div className="cart-item-image">
                <img src={item.gallery[0]} alt={item.name} />
              </div>
            </div>
          ))}
        </div>
        <div className="cart-total-container">
          <span className="cart-total-label">Total:</span>
          <span className="cart-total-amount">
            {cart.length > 0 && (cart[0].price?.currency?.symbol || "$")}
            {totalPrice.toFixed(2)}
          </span>
        </div>
        <button
          onClick={handlePlaceOrder}
          disabled={cart.length === 0 || loading}
          className={`place-order-btn ${
            cart.length === 0 ? "disabled" : "active"
          }`}
        >
          {loading ? "Placing Order..." : "Place Order"}
        </button>
        {error && (
          <p className="error">Failed to place order. Please try again.</p>
        )}
      </div>
    </>
  );

  // Render the overlay into a modal container if it exists, or fall back to document.body.
  const modalContainer = document.getElementById("modal-root") || document.body;
  return ReactDOM.createPortal(overlayContent, modalContainer);
};

export default CartOverlay;
