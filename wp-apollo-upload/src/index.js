import React from 'react';
import ReactDOM from 'react-dom';
import './index.css';
import App from './App';
import * as serviceWorker from './serviceWorker';

/**
 * In the wp-admin, this div is setup by render_admin_page in wp-graphql-file-upload.php for the HubView admin page where the
 * React App is enqueued.
 * */
ReactDOM.render(<App />, document.getElementById('wp-apollo-upload'));

// If you want your app to work offline and load faster, you can change
// unregister() to register() below. Note this comes with some pitfalls.
// Learn more about service workers: https://bit.ly/CRA-PWA
serviceWorker.unregister();
