import React from 'react';
import { render } from 'react-dom';
import ApolloClient from 'apollo-boost';
import { ApolloProvider } from 'react-apollo';
import { Mutation } from 'react-apollo';
import gql from 'graphql-tag';
import { Upload, message, Button, Icon } from 'antd';
import './index.css';

const client = new ApolloClient({
	// Change this to the URL of your WordPress site.
	uri: 'http://denverpost.test/graphql'
});

const props = {
	name: 'file',
	action: '//jsonplaceholder.typicode.com/posts/',
	headers: {
		authorization: 'authorization-text'
	},
	onChange(info) {
		if (info.file.status !== 'uploading') {
			console.log(info.file, info.fileList);
		}
		if (info.file.status === 'done') {
			message.success(`${info.file.name} file uploaded successfully`);
		} else if (info.file.status === 'error') {
			message.error(`${info.file.name} file upload failed.`);
		}
	}
};

const App = props => (
	<ApolloProvider client={client}>
		<div className="App">
			<h2 className="title">
				WP-GraphQL File Upload{' '}
				<span className="emoji" role="img" aria-label="rocket">
					🚀
				</span>
			</h2>
			<Upload {...props}>
				<Button>
					<Icon type="upload" /> Click to Upload
				</Button>
			</Upload>
		</div>
	</ApolloProvider>
);

render(<App />, document.getElementById('root'));
