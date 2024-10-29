import React from 'react'
import {store} from 'Store/store';
import {Alert, Collapse} from "@mui/material";
import {useSelector} from "react-redux";
import {getNotificationsList, appendNotification, removeNotification} from "Store/slices/notifications";
import {TransitionGroup} from 'react-transition-group';

export const showSuccessMessage = (message) => {
    showMessage("success", message);
}

export const showErrorMessage = (message) => {
    showMessage("error", message);
}

const showMessage = (type, message) => {
    const dispatch = store.dispatch;
    const notification = dispatch(appendNotification({type: type, message: message})).payload;
    setTimeout(() => dispatch(removeNotification(notification)), 3000);
}

export const NotificationsBlock = (props) => {
    const notifications = useSelector(getNotificationsList)

    return (
        <TransitionGroup style={{padding: '1rem'}}>
            {notifications.map(
                (message, index) => (
                    <Collapse key={index} timeout={300}>
                        <Alert
                            variant="filled"
                            severity={message.type}
                            style={{marginBottom: '1rem'}}
                        >
                            {message.message}
                        </Alert>
                    </Collapse>
                )
            )}
        </TransitionGroup>
    )
}


