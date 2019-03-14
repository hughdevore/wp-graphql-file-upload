import React, { Component } from 'react';
import { ApolloClient } from 'apollo-client';
import { createHttpLink } from 'apollo-link-http';
import { InMemoryCache } from 'apollo-cache-inmemory';
import { ApolloProvider } from 'react-apollo';
import { Mutation } from 'react-apollo';
import gql from 'graphql-tag';
import { Input, Button } from 'antd';
import '../index.css';
import '../App.css';

/**
 * Change this to the URL of your WordPress site
 */
const uri = 'http://denverpost.test/graphql';
const httpLink = createHttpLink({
	uri
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

class Login extends Component {
	state = {
		username: '',
		password: ''
	};

	handleChange = e => {
		if (e.target && e.target.name && e.target.value) {
			const { name, value } = e.target;
			const state = {};
			state[name] = value;
			this.setState(state);
		}
	};

	render() {
		const { authenticate } = this.props;

		const login_variables = {
			input: {
				clientMutationId: 'loginForCreateMediaItemTest',
				username: this.state.username,
				password: this.state.password
			}
		};

		return (
			<ApolloProvider client={noAuthClient}>
				<Mutation mutation={LOGIN_MUTATION} variables={login_variables}>
					{(login, { loading, error, data }) => {
						if (error) return <p>Error!</p>;
						if (loading) return <p>Loading...</p>;
						if (data && data.login && data.login.authToken) {
							// Set the authToken in local storage so we can use it in our apollo client
							localStorage.setItem(
								'authToken',
								data.login.authToken
							);
							// Let the app know we're authenticated
							authenticate();
						}
						return (
							<div className="App">
								<h1 className="App-header">
									Login to your local WordPress site at:{' '}
									<a className="App-link" href={uri}>
										<span>{uri}</span>
									</a>
								</h1>
								<form>
									<Input.Group size="large">
										<Input
											name="username"
											placeholder="username"
											onChange={this.handleChange}
											value={this.state.username}
											style={{
												width: '100%',
												display: 'block',
												margin: '10px 0'
											}}
										/>
										<Input.Password
											name="password"
											placeholder="password"
											onChange={this.handleChange}
											value={this.state.password}
											style={{
												width: '100%',
												display: 'block',
												margin: '10px 0'
											}}
										/>
										<Button
											onClick={login}
											type="primary"
											size="large"
											style={{ marginTop: '10px' }}
										>
											Login
										</Button>
									</Input.Group>
								</form>
							</div>
						);
					}}
				</Mutation>
			</ApolloProvider>
		);
	}
}

export default Login;
