// src/main.jsx
import React from "react";
import ReactDOM from "react-dom/client"; // Updated import for React 18
import { ApolloProvider } from "@apollo/client";
import App from "./App";
import client from "./graphql/apolloClient";  // Import Apollo Client

// Create a root element and render using createRoot
const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
  <ApolloProvider client={client}>  {/* Provide Apollo Client to the app */}
    <App />
  </ApolloProvider>
);
