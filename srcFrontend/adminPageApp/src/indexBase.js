import React from "react";
import {createRoot} from 'react-dom/client';
import {Provider} from 'react-redux';
import {store} from 'Store/store';
import App from "./base/App";

import './app/i18n';


const container = document.getElementById('root');
const root = createRoot(container);

root.render(
    <React.StrictMode>
        <Provider store={store}>
            <App/>
        </Provider>
    </React.StrictMode>
);
