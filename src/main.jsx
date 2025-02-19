import React from "react";
import ReactDOM from "react-dom";
import { ApolloProvider } from "@apollo/client";
import { BrowserRouter } from "react-router-dom";
import App from "./App";
import client from "./graphql/apolloClient";

ReactDOM.render(
  <ApolloProvider client={client}>
    <BrowserRouter basename="/scandiweb">
      <App />
    </BrowserRouter>
  </ApolloProvider>,
  document.getElementById("root")
);
