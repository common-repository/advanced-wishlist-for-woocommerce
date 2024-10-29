import React from 'react'
import { render } from 'react-dom'
import { Provider } from 'react-redux'
import { store } from 'Store/store'
import App from './base/App'

import './app/i18n'

const root = document.getElementById('awl-client-app')
render(
    <Provider store={store}>
        <App wishlistId={root.getAttribute('data-wishlist-id')} mutable={root.getAttribute('data-mutable')}/>
    </Provider>,
    root
)
