import React from "react";
import { useCart } from "../context/CartContext";
import "./CartOverlay.css";
import { gql, useMutation } from "@apollo/client";

// Updated mutation that accepts an array of order products
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

  // Calculate total quantity and total price from the cart
  const totalQuantity = cart.reduce((sum, item) => sum + item.quantity, 0);
  const totalPrice = cart.reduce(
    (sum, item) => sum + (item.price?.amount || 0) * item.quantity,
    0
  );

  // Use the mutation with an onCompleted callback that clears the cart
  const [createOrder, { loading, error }] = useMutation(CREATE_ORDER, {
    onCompleted: () => {
      clearCart();
      alert("Order placed successfully!");
    },
  });

  // Prepare the order payload from cart items and call the mutation
  const handlePlaceOrder = () => {
    if (cart.length === 0) return;

    const orderProducts = cart.map((item) => ({
      productId: item.id,
      quantity: item.quantity,
      totalPrice: (item.price?.amount || 0) * item.quantity,
    }));

    createOrder({ variables: { products: orderProducts } });
  };

  // If quantity equals 1, remove the item; otherwise, decrease quantity
  const handleDecrease = (item) => {
    if (item.quantity === 1) {
      removeFromCart(item);
    } else {
      updateQuantity(item, -1);
    }
  };

  return (
    <>
      {/* Backdrop to close the overlay when clicked */}
      <div className="backdrop" onClick={onClose} />
      <div className="cart-overlay">
        <h3>
          My Bag, {totalQuantity} {totalQuantity === 1 ? "Item" : "Items"}
        </h3>

        <div className="cart-items-container">
          {cart.map((item) => (
            <div key={item.id} className="cart-item">
              {/* Product Details */}
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

              {/* Quantity Controls */}
              <div className="cart-item-quantity">
                <button onClick={() => handleDecrease(item)}>-</button>
                <span>{item.quantity}</span>
                <button onClick={() => updateQuantity(item, 1)}>+</button>
              </div>

              {/* Product Image */}
              <div className="cart-item-image">
                <img src={item.gallery[0]} alt={item.name} />
              </div>
            </div>
          ))}
        </div>

        {/* Cart Total */}
        <div className="cart-total-container">
          <span className="cart-total-label">Total:</span>
          <span className="cart-total-amount">
            {cart.length > 0 && (cart[0].price?.currency?.symbol || "$")}
            {totalPrice.toFixed(2)}
          </span>
        </div>

        {/* Place Order Button */}
        <button
          onClick={handlePlaceOrder}
          disabled={cart.length === 0 || loading}
          className={`place-order-btn ${cart.length === 0 ? "disabled" : "active"}`}
        >
          {loading ? "Placing Order..." : "Place Order"}
        </button>

        {/* Display error message if mutation fails */}
        {error && <p className="error">Failed to place order. Please try again.</p>}
      </div>
    </>
  );
};

export default CartOverlay;
