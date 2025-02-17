import React, { useState } from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Header from "./components/Header";
import CartOverlay from "./components/CartOverlay";
import { CartProvider } from "./context/CartContext";
import ProductListingPage from "./components/ProductListingPage";
import ProductDetailsPage from "./components/ProductDetailsPage";
import "./App.css";

const App = () => {
  const [isOverlayVisible, setIsOverlayVisible] = useState(false);
  const toggleOverlay = () => setIsOverlayVisible((prev) => !prev);

  return (
    <CartProvider>
      <BrowserRouter basename={window.location.pathname.split('/')[1] || ''}>
        <Header toggleOverlay={toggleOverlay} />
        {isOverlayVisible && <CartOverlay onClose={toggleOverlay} />}
        <Routes>
          <Route path="/" element={<ProductListingPage />} />
          <Route path="/all" element={<ProductListingPage />} />
          <Route path="/tech" element={<ProductListingPage />} />
          <Route path="/clothes" element={<ProductListingPage />} />
          <Route
            path="/product/:id"
            element={<ProductDetailsPage toggleOverlay={toggleOverlay} />}
          />
          <Route path="*" element={<ProductListingPage />} />
        </Routes>
      </BrowserRouter>
    </CartProvider>
  );
};

export default App;
