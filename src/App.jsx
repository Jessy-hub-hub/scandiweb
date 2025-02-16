// src/App.jsx
import React, { useState } from "react";
import { Routes, Route } from "react-router-dom"; // Remove BrowserRouter import
import Header from "./components/Header";
import CartOverlay from "./components/CartOverlay";
import { CartProvider } from "./context/CartContext";
import ProductListingPage from "./components/ProductListingPage";
import ProductDetailsPage from "./components/ProductDetailsPage";
import "./App.css";

const App = () => {
  const [isOverlayVisible, setIsOverlayVisible] = useState(false);

  const toggleOverlay = () => {
    setIsOverlayVisible((prev) => !prev);
  };

  return (
    <CartProvider>
      <Header toggleOverlay={toggleOverlay} />
      {isOverlayVisible && <CartOverlay onClose={toggleOverlay} />}
      <Routes>
        <Route path="/" element={<ProductListingPage />} />
        <Route path="/products" element={<ProductListingPage />} />
        <Route
          path="/product/:id"
          element={<ProductDetailsPage toggleOverlay={toggleOverlay} />}
        />
      </Routes>
    </CartProvider>
  );
};

export default App;
