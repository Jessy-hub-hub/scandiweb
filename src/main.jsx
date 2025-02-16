import React from "react";
import ReactDOM from "react-dom/client";
import { ApolloProvider } from "@apollo/client";
import { HashRouter } from "react-router-dom"; // Keep HashRouter ONLY here
import App from "./App";
import client from "./graphql/apolloClient";

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
  <ApolloProvider client={client}>
    <HashRouter> {/* This should be the only Router */}
      <App />
    </HashRouter>
  </ApolloProvider>
);
