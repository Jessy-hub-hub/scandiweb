import React, { useState } from 'react';
import { useMutation } from '@apollo/client';
import { ADD_TO_CART } from '../graphql/mutations';
import './ProductCard.css';

const ProductCard = ({ product }) => {
  const [quantity, setQuantity] = useState(1);
  const [addToCart, { loading, error }] = useMutation(ADD_TO_CART);

  const handleAddToCart = (productId, quantity, attributes) => {
    addToCart({
      variables: { productId, quantity, attributes },
      onCompleted: (data) => {
        console.log('Item added to cart:', data.addToCart);
        // Optionally update local state or context here to reflect cart update
      },
      onError: (err) => {
        console.error('Error adding to cart:', err);
      }
    });
  };

  const handleQuantityChange = (change) => {
    setQuantity((prevQuantity) => Math.max(1, prevQuantity + change));
  };

  const { id, name, price, attributes } = product;

  return (
    <div className="product-card">
      <img src={product.image} alt={name} />
      <h3>{name}</h3>
      <p>${price}</p>
      <div className="quantity">
        <button onClick={() => handleQuantityChange(-1)}>-</button>
        <span>{quantity}</span>
        <button onClick={() => handleQuantityChange(1)}>+</button>
      </div>
      <button
        onClick={() => handleAddToCart(id, quantity, attributes)}
        disabled={loading}
      >
        {loading ? 'Adding...' : 'Add to Cart'}
      </button>
      {error && <p style={{ color: 'red' }}>Error: {error.message}</p>}
    </div>
  );
};

export default ProductCard;
