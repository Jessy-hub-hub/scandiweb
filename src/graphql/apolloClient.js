// src/graphql/apolloClient.js
import { ApolloClient, InMemoryCache, HttpLink, ApolloLink } from "@apollo/client";

// Use your actual GraphQL API endpoint
const client = new ApolloClient({
  link: new HttpLink({ uri: 'http://localhost:8000/graphql' }),  // Adjust this URL
  cache: new InMemoryCache(),
});

export default client;
