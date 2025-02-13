import React, { createContext, useState, useContext } from "react";

// Create the context (this remains internal to the module)
const CartContext = createContext();

// The provider is a React component, which is safe for Fast Refresh.
export const CartProvider = ({ children }) => {
  const [cart, setCart] = useState([]);

  const addToCart = (product, options) => {
    setCart((prevCart) => {
      // Look for an existing item with the same product id and options.
      const existingItem = prevCart.find(
        (item) =>
          item.id === product.id &&
          JSON.stringify(item.options) === JSON.stringify(options)
      );

      if (existingItem) {
        // If found, increase its quantity.
        return prevCart.map((item) =>
          item.id === product.id &&
          JSON.stringify(item.options) === JSON.stringify(options)
            ? { ...item, quantity: item.quantity + 1 }
            : item
        );
      } else {
        // Otherwise, add it as a new item.
        return [...prevCart, { ...product, options, quantity: 1 }];
      }
    });
    console.log("Added to cart:", product.name, "with options:", options);
  };

  const removeFromCart = (product) => {
    setCart((prevCart) => prevCart.filter((item) => item.id !== product.id));
  };

  const updateQuantity = (product, increment) => {
    setCart((prevCart) =>
      prevCart.map((item) =>
        item.id === product.id
          ? { ...item, quantity: item.quantity + increment }
          : item
      )
    );
  };

  const clearCart = () => {
    setCart([]);
  };

  return (
    <CartContext.Provider
      value={{ cart, addToCart, removeFromCart, updateQuantity, clearCart }}
    >
      {children}
    </CartContext.Provider>
  );
};

// Export a custom hook that encapsulates useContext for consuming the cart.
export const useCart = () => useContext(CartContext);
