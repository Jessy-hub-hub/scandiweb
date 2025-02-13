import React, { useState } from "react";
import { useParams } from "react-router-dom";
import { useQuery } from "@apollo/client";
import { GET_PRODUCT_BY_ID } from "../graphql/queries";
import { useCart } from "../context/CartContext"; // Using custom hook
import "./ProductDetailsPage.css";

const ProductDetailsPage = ({ toggleOverlay }) => {
  const { id } = useParams();
  const { loading, error, data } = useQuery(GET_PRODUCT_BY_ID);

  // State for selected options and image navigation
  const [selectedAttributes, setSelectedAttributes] = useState({});
  const [currentImageIndex, setCurrentImageIndex] = useState(0);

  // Retrieve addToCart from the custom hook
  const { addToCart } = useCart();

  if (loading) return <p>Loading...</p>;
  if (error) return <p>Error: {error.message}</p>;

  // Find the current product by id
  const product = data.products.find((prod) => String(prod.id) === id);
  if (!product) return <p>Product not found.</p>;

  const handleImageNavigation = (direction) => {
    if (direction === "prev") {
      setCurrentImageIndex((prevIndex) =>
        prevIndex === 0 ? product.gallery.length - 1 : prevIndex - 1
      );
    } else {
      setCurrentImageIndex((prevIndex) =>
        prevIndex === product.gallery.length - 1 ? 0 : prevIndex + 1
      );
    }
  };

  // Update selected option for a given attribute
  const handleAttributeSelect = (attributeId, value) => {
    setSelectedAttributes((prev) => ({ ...prev, [attributeId]: value }));
  };

  // Check if all attributes have been selected
  const allAttributesSelected = product.attributes.every(
    (attribute) => selectedAttributes.hasOwnProperty(attribute.id)
  );

  return (
    <div className="product-details-page">
      {/* Left Column: Thumbnail Gallery and Main Image */}
      <div className="product-image-section">
        <div className="thumbnails">
          {product.gallery.map((img, index) => (
            <img
              key={index}
              src={img}
              alt={`Thumbnail ${index}`}
              className={`thumbnail ${currentImageIndex === index ? "active" : ""}`}
              onClick={() => setCurrentImageIndex(index)}
            />
          ))}
        </div>
        <div className="main-image-wrapper">
          <button className="arrow left-arrow" onClick={() => handleImageNavigation("prev")}>
            &#9664;
          </button>
          <img
            className="main-image"
            src={product.gallery[currentImageIndex]}
            alt={product.name}
          />
          <button className="arrow right-arrow" onClick={() => handleImageNavigation("next")}>
            &#9654;
          </button>
        </div>
      </div>

      {/* Right Column: Product Details */}
      <div className="product-details-section">
        {/* Product Name */}
        <h1 className="product-name">{product.name}</h1>

        {/* Render each attribute set dynamically */}
        {product.attributes.map((attribute) => (
          <div key={attribute.id} className="attribute-set">
            <h3>{attribute.name}</h3>
            <div data-testid={`product-attribute-${attribute.id.toLowerCase()}`}>
              {attribute.items.map((item) => (
                <button
                  key={item.id}
                  onClick={() => handleAttributeSelect(attribute.id, item.value)}
                  className={`attribute-button ${
                    selectedAttributes[attribute.id] === item.value ? "selected" : ""
                  }`}
                  style={attribute.type === "swatch" ? { backgroundColor: item.value } : {}}
                >
                  {attribute.type !== "swatch" ? item.displayValue : ""}
                </button>
              ))}
            </div>
          </div>
        ))}

        {/* Price */}
        <h3>Price</h3>
        <p className="product-price">
          {product.prices[0].currency.symbol}
          {product.prices[0].amount.toFixed(2)}
        </p>

        {/* Add to Cart Button */}
        <button
          className="add-to-cart-btn"
          data-testid="add-to-cart"
          disabled={!allAttributesSelected}
          onClick={() => {
            // Attach a default price (for use in CartOverlay)
            const productWithPrice = { ...product, price: product.prices[0] };
            addToCart(productWithPrice, selectedAttributes);
            // Toggle the cart overlay open/close
            toggleOverlay();
          }}
        >
          Add to Cart
        </button>

        {/* Product Description */}
        <div data-testid="product-description" className="product-description">
          {product.description}
        </div>
      </div>
    </div>
  );
};

export default ProductDetailsPage;
