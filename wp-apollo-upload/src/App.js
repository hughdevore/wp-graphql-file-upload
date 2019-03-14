import React, { Component } from 'react';
import { ApolloClient } from 'apollo-client';
import { createHttpLink } from 'apollo-link-http';
import { setContext } from 'apollo-link-context';
import { InMemoryCache } from 'apollo-cache-inmemory';
import { ApolloProvider } from 'react-apollo';
import { Mutation } from 'react-apollo';
import gql from 'graphql-tag';
import { Upload, Button, Icon } from 'antd';
import Login from './components/Login';

/**
 * Change this to the URL of your WordPress site
 */
const uri = 'http://denverpost.test/graphql';
const httpLink = createHttpLink({
	uri
});

const authLink = setContext((_, { headers }) => {
	// get the authentication token from local storage if it exists
	const token = localStorage.getItem('authToken');
	// return the headers to the context so httpLink can read them
	return {
		headers: {
			...headers,
			authorization: token ? `Bearer ${token}` : null
		}
	};
});

const client = new ApolloClient({
	link: authLink.concat(httpLink),
	cache: new InMemoryCache()
});

const UPLOAD_FILE_MUTATION = gql`
	mutation UPLOAD_FILE_MUTATION($input: CreateMediaItemInput!) {
		createMediaItem(input: $input) {
			clientMutationId
			mediaItem {
				id
				mediaItemId
				title
				uri
				sourceUrl
			}
		}
	}
`;

const upload_variables = {
	input: {
		clientMutationId: 'testCreateMediaItem',
		file: null
	}
};

/**
 * Our WP-Apollo-Upload app
 */
class App extends Component {
	state = {
		isAuthenticated: false
	};

	authenticate = () => {
		this.setState({
			isAuthenticated: true
		});
	};

	render() {
		if (!this.state.isAuthenticated) {
			return <Login authenticate={this.authenticate} />;
		} else {
			return (
				<ApolloProvider client={client}>
					<div className="App">
						<h2 className="App-header">
							WP-GraphQL File Upload{' '}
							<span
								className="emoji"
								role="img"
								aria-label="rocket"
							>
								🚀
							</span>
						</h2>
						<Mutation
							mutation={UPLOAD_FILE_MUTATION}
							variables={upload_variables}
						>
							{(createMediaItem, { error, loading, data }) => {
								if (error) return <p>Error!</p>;
								if (loading) return <p>Loading...</p>;
								if (data) {
									console.log(data);
								}
								return (
									<Upload>
										<Button
											type="primary"
											onClick={createMediaItem}
										>
											<Icon type="upload" /> Click to
											Upload
										</Button>
									</Upload>
								);
							}}
						</Mutation>
					</div>
				</ApolloProvider>
			);
		}
	}
}

export default App;
