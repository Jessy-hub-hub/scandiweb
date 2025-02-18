import React from "react";
import ReactDOM from "react-dom/client";
import { ApolloProvider } from "@apollo/client";
import { BrowserRouter } from "react-router-dom";
import App from "./App";
import client from "./graphql/apolloClient";

// Vite automatically loads .env.production in production builds.
// If VITE_BASENAME is not set, default to "/"
const basename = import.meta.env.VITE_BASENAME || "/";

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
  <ApolloProvider client={client}>
    <BrowserRouter basename={basename}>
      <App />
    </BrowserRouter>
  </ApolloProvider>
);
