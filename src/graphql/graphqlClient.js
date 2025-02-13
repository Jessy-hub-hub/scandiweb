// src/graphql/graphqlClient.js
import { ApolloClient, InMemoryCache, HttpLink } from '@apollo/client';

const client = new ApolloClient({
  uri: 'http://localhost:8000/graphql', // Make sure your GraphQL server URL is correct
  cache: new InMemoryCache(),
});

export default client;
