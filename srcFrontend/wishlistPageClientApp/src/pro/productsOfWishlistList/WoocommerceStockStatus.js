import { Container } from '@mui/material'
import React from 'react'
import { useTranslation } from 'react-i18next'

const WoocommerceStockStatus = (props) => {
    const { stockClass, stockText } = props
    const { t } = useTranslation()

    const availabilityText = !!stockText ? stockText : t('In stock')
    const availabilityClass = stockClass !== null ? stockClass : 'in-stock'

    let stockLabelColor
    if (availabilityClass === 'in-stock' || availabilityClass === 'available-on-backorder') {
        stockLabelColor = 'green'
    } else if (availabilityClass === 'out-of-stock') {
        stockLabelColor = 'red'
    } else {
        stockLabelColor = 'black'
    }

    return <Container style={{ color: stockLabelColor }}>
        {availabilityText}
    </Container>
}

export default WoocommerceStockStatus
