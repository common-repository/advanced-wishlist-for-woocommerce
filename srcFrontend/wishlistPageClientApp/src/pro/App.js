import React from 'react';
import ItemsOfWishlistList from "./productsOfWishlistList/ItemsOfWishlistList";
import "BaseComponents/App.css";

function App(props) {
    const {mutable} = props;

    return (
        <ItemsOfWishlistList wishlistId={props.wishlistId} mutable={mutable}/>
    );
}

export default App;
