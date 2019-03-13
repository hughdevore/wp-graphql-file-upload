import React, { Component } from 'react';
import { ApolloClient } from 'apollo-client';
import { createHttpLink } from 'apollo-link-http';
import { setContext } from 'apollo-link-context';
import { InMemoryCache } from 'apollo-cache-inmemory';
import { ApolloProvider } from 'react-apollo';
import { Mutation } from 'react-apollo';
import gql from 'graphql-tag';
import { Input, Upload, Button, Icon } from 'antd';
import './index.css';

const httpLink = createHttpLink({
	// Change this to the URL of your WordPress site.
	uri: 'http://denverpost.test/graphql'
});

const authLink = setContext((_, { headers }) => {
	// get the authentication token from local storage if it exists
	const token = localStorage.getItem('authToken');
	// return the headers to the context so httpLink can read them
	if (!token) return;
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

const noAuthClient = new ApolloClient({
	link: httpLink,
	cache: new InMemoryCache()
});

/**
 * Login to your wordpress site so that you can mutate against it
 */
const LOGIN_MUTATION = gql`
	mutation LOGIN_FOR_CREATE_MEDIA_ITEM_TEST($input: LoginInput!) {
		login(input: $input) {
			clientMutationId
			authToken
			refreshToken
		}
	}
`;

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
 * Our app
 */
class App extends Component {
	state = {
		isAuthenticated: false,
		username: '',
		password: ''
	};

	handleChange = e => {
		const name = e.target.name;
		const value = e.target.value;
		const state = {};
		state[name] = value;
		this.setState(prevState => {
			return {
				...prevState,
				...state
			};
		});
	};

	authenticate = () => {
		this.setState(prevState => {
			return {
				...prevState,
				isAuthenticated: true
			};
		});
	};

	render() {
		const login_variables = {
			input: {
				clientMutationId: 'loginForCreateMediaItemTest',
				username: this.state.username,
				password: this.state.password
			}
		};

		if (!this.state.isAuthenticated) {
			return (
				<ApolloProvider client={noAuthClient}>
					<Mutation
						mutation={LOGIN_MUTATION}
						variables={login_variables}
					>
						{(login, { loading, error, data }) => {
							if (error) return <p>Error!</p>;
							if (loading) return <p>Loading...</p>;
							if (data) {
								// Set the authToken in local storage so we can use it in our apollo client
								localStorage.setItem(
									'authToken',
									data.login.authToken
								);
								// Let the app know we're authenticated
								this.authenticate();
							}
							return (
								<div>
									<h1>Login to your WordPress</h1>
									<form>
										<Input
											name="username"
											placeholder="username"
											onChange={this.handleChange}
											value={this.state.username}
										/>
										<Input.Password
											name="password"
											placeholder="password"
											onChange={this.handleChange}
											value={this.state.password}
										/>
										<Button onClick={login} type="primary">
											Login
										</Button>
									</form>
								</div>
							);
						}}
					</Mutation>
				</ApolloProvider>
			);
		} else {
			return (
				<ApolloProvider client={client}>
					<div className="App-header">
						<h2 className="app-title">
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
