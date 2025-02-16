import React, { useState } from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
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
      {/* Removed the basename so links render as "/tech" etc. */}
      <Router>
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
      </Router>
    </CartProvider>
  );
};

export default App;
