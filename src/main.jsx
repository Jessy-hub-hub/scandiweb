import React from "react";
import ReactDOM from "react-dom/client";
import { ApolloProvider } from "@apollo/client";
import { BrowserRouter } from "react-router-dom";
import App from "./App";
import client from "./graphql/apolloClient";

ReactDOM.createRoot(document.getElementById("root")).render(
  <React.StrictMode>
    <ApolloProvider client={client}>
      {/* No more basename, so routes are at /all, /tech, etc. */}
      <BrowserRouter>
        <App />
      </BrowserRouter>
    </ApolloProvider>
  </React.StrictMode>
);
