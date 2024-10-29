import React from 'react';
import {useTranslation} from 'react-i18next';
import {useDispatch, useSelector} from "react-redux";
import {
    Button,
    MenuItem,
    TextField,
    Modal,
    Grid,
    Typography,
    Box,
    Tabs,
    Tab,
    Backdrop,
    CircularProgress,
    Autocomplete,
    Alert
} from "@mui/material";
import PropTypes from 'prop-types';

import {
    getPromotionalEmailPreview,
    loadPromotionalEmailPreviewAsync,
    loadPromotionalEmailSaveDraftAsync,
    loadPromotionalEmailCalculateEmailReceiversAsync,
    loadPromotionalEmailSendAsync,
    getPromotionalEmailEmailReceiversLabel
} from "Store/slices/promotionalEmailSlice";

import {
    getWcCoupons,
    loadWcCouponsAsync,
} from "Store/slices/wcCouponsSlice";

import {MailOutline as MailOutlineIcon, Send as SendIcon} from '@mui/icons-material';

import "./PromotionalEmailModal.css";

function TabPanel(props) {

    const {children, value, index, ...other} = props;

    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`simple-tabpanel-${index}`}
            aria-labelledby={`simple-tab-${index}`}
            style={{boxShadow: '0 0 25px -4px rgb(68 138 133 / 40%)', margin: '0 10px 10px 0',}}
            {...other}
        >
            <Box sx={{p: 3}}>
                <Typography>{children}</Typography>
            </Box>
        </div>
    );
}

TabPanel.propTypes = {
    children: PropTypes.node,
    index: PropTypes.number.isRequired,
    value: PropTypes.number.isRequired,
};

function a11yProps(index) {
    return {
        id: `simple-tab-${index}`,
        'aria-controls': `simple-tabpanel-${index}`,
    };
}

function HtmlTemplateTab(props) {

    const {t} = useTranslation();

    const handleChange = (event) => {
        props.handleHtmlContent(event.target.value);
    };

    const handleTextUpdated = (text) => {
        props.handleHtmlContent(text)
    };

    const textareaEl = React.useRef(null)

    React.useEffect(() => {
        initEditor();
    }, []);

    const initEditor = () => {

        if (!tinymce) {
            return;
        }

        const id = textareaEl.current.id;

        // Destroy any existing editor so that it can be re-initialized when popup opens.
        if (tinymce.get(id)) {
            var restoreTextMode = tinymce.get(id).isHidden();
            wp.editor.remove(id);
        }

        wp.editor.initialize(id, {
            tinymce: {
                wpautop: true,
                init_instance_callback: (editor) => {
                    editor.on('Change', (e) => {
                        handleTextUpdated(editor.getContent());
                    });
                }
            },
            quicktags: true,
            mediaButtons: true
        });
    }

    return (
        <div>
            <textarea
                rows={10}
                ref={textareaEl}
                id="html-textarea"
                style={{width: '100%'}}
                onChange={handleChange}
            >
                {props.htmlContent}
            </textarea>
            <p><strong>{t('This field lets you modify the main content of the HTML version of the email.')}</strong></p>
        </div>
    );
}

function PlainTemplateTab(props) {

    const {t} = useTranslation();

    const handleChange = (event) => {
        props.handlePlainContent(event.target.value);
    };

    return (
        <div>
            <TextField
                label="Email text"
                multiline
                rows={10}
                value={props.plainContent}
                onChange={handleChange}
                sx={{width: '100%'}}
            />
            <p><strong>{t('This field lets you modify the main content of the text version of the email')}</strong></p>
        </div>
    );
}

function EmailTemplateSupportedTags() {

    const {t} = useTranslation();

    return (
        <div>
            <Typography id="modal-modal-title" variant="h7" component="h5" sx={{color: '#007694'}}>
                {t('You can use the following placeholder:')}
            </Typography>
            <p>{'{user_name} {user_email} {user_first_name} {user_last_name} {product_image} {product_name} {product_price} {coupon_code} {coupon_amount} {product_url} {add_to_cart_url}'}</p>
        </div>
    );
}

function EmailTemplateHeaderNote() {

    const {t} = useTranslation();

    return (
        <p><span style={{color: '#a20000'}}><strong>{t('Note: ')}</strong></span>
            <span>{t('you can customize the header in WooCommerce › Settings › Emails.')}</span></p>
    );
}

function Coupon(props) {

    const {t} = useTranslation();

    const coupons = useSelector(getWcCoupons)

    const [open, setOpen] = React.useState(false);

    const [options, setOptions] = React.useState([]);

    const wcCouponsStatus = useSelector((state) => state.wcCoupons.status);

    const loading = open && wcCouponsStatus === 'loading';

    const [search, setSearch] = React.useState('')

    const dispatch = useDispatch();

    const [timeout, setTimeoutValue] = React.useState(null);

    const searchCoupons = (search) => {

        timeout && clearTimeout(timeout)

        if (!search.length) {
            setOptions([])
            return;
        }

        setTimeoutValue(setTimeout(() => {
            console.log(search)
            dispatch(loadWcCouponsAsync({limit: 10, term: search}))
        }, 1000))
    }

    React.useEffect(() => {
        searchCoupons(search)
    }, [search]);

    React.useEffect(() => {
        setOptions(coupons)
    }, [coupons]);

    return (
        <div class="wc-coupons-select">
            <Autocomplete
                id="asynchronous-demo"
                sx={{width: '100%'}}
                open={open}
                onOpen={() => {
                    setOpen(true);
                }}
                onClose={() => {
                    setOpen(false);
                }}
                isOptionEqualToValue={(option, value) => option.title === value.title}
                getOptionLabel={(option) => option.title}
                options={options}
                loading={loading}
                value={props.coupon}
                onChange={(event, newValue) => {
                    props.setCoupon(newValue);
                }}
                inputValue={search}
                onInputChange={(event, newInputValue) => {
                    setSearch(newInputValue);
                }}
                renderInput={(params) => (
                    <TextField
                        {...params}
                        label={t('Select coupon')}
                        placeholder={t('type to search')}
                        InputProps={{
                            ...params.InputProps,
                            endAdornment: (
                                <React.Fragment>
                                    {loading ? <CircularProgress color="inherit" size={20}/> : null}
                                    {params.InputProps.endAdornment}
                                </React.Fragment>
                            ),
                        }}
                    />
                )}
            />
            <p><strong>{t('This field lets you choose coupon to use for the email.')}</strong></p>
        </div>
    );
}

function TemplateTabs(props) {

    const {t} = useTranslation();

    return (
        <Box sx={{width: '100%'}}>
            <Box sx={{borderBottom: 1, borderColor: 'divider', mr: '10px'}}>
                <Tabs value={props.typeEmail} onChange={props.handleTypeEmail} aria-label="basic tabs example">
                    <Tab label={t('E-mail HTML content')} {...a11yProps(0)} />
                    <Tab label={t('E-mail Text content')} {...a11yProps(1)} />
                </Tabs>
            </Box>
            <TabPanel value={props.typeEmail} index={0}>
                <HtmlTemplateTab htmlContent={props.htmlContent} handleHtmlContent={props.handleHtmlContent}/>
                <EmailTemplateSupportedTags/>
                <EmailTemplateHeaderNote/>
            </TabPanel>
            <TabPanel value={props.typeEmail} index={1}>
                <PlainTemplateTab plainContent={props.plainContent} handlePlainContent={props.handlePlainContent}/>
                <EmailTemplateSupportedTags/>
                <EmailTemplateHeaderNote/>
            </TabPanel>
            <Coupon coupon={props.coupon} setCoupon={props.setCoupon}/>
        </Box>
    );
}

const stylePreview = {
    position: 'absolute',
    top: '50%',
    left: '55%',
    transform: 'translate(-50%, -50%)',
    width: '80%',
    bgcolor: 'background.paper',
    border: '2px solid #000',
    boxShadow: 24,
    p: 0,
    height: '90%',
};

const styleSend = {
    position: 'absolute',
    top: '50%',
    left: '50%',
    transform: 'translate(-50%, -50%)',
    width: 450,
    bgcolor: 'background.paper',
    border: '2px solid #000',
    boxShadow: 24,
    p: 0,
    height: 450,
};

const PromotionalEmailModal = (props) => {

    const {t} = useTranslation();

    const dispatch = useDispatch();

    const [open, setOpen] = React.useState(false);
    const handleOpen = () => setOpen(true);
    const handleClose = () => {
        setOpen(false);
        handlePreviewEmailView();
        setShowSendEmailSuccess(false)
        setShowSaveDraftSuccess(false)
    }

    const [view, setView] = React.useState(0);

    const handlePreviewEmailView = () => setView(0);

    const handleSendEmailView = () => {
        setView(1);
        dispatch(loadPromotionalEmailCalculateEmailReceiversAsync({productID: props.productId, userID: props.userId ? props.userId : ''}))
    };

    const emailPreview = useSelector(getPromotionalEmailPreview)

    const previewEmailStatus = useSelector((state) => state.promotionalEmail.status);

    const isLoading = () => previewEmailStatus === 'loading';

    const [typeEmail, setTypeEmail] = React.useState(0);

    const handleTypeEmail = (event, newValue) => setTypeEmail(newValue);

    const [htmlContent, setHtmlContent] = React.useState('html content');

    const handleHtmlContent = (text) => setHtmlContent(text);

    const [plainContent, setPlainContent] = React.useState('plain content');

    const handlePlainContent = (text) => setPlainContent(text);

    const [coupon, setCoupon] = React.useState(null);

    const getEmailParams = () => {
        return {
            type: typeEmail === 0 ? 'html' : 'plain',
            htmlContent: htmlContent,
            plainContent: plainContent,
            productID: props.productId,
            userID: props.userId ? props.userId : '',
            coupon: coupon ? coupon.code : ''
        }
    }

    React.useEffect(() => {
        open && dispatch(loadPromotionalEmailPreviewAsync(getEmailParams()))
    }, [typeEmail, htmlContent, plainContent, coupon, open])

    const handleSendEmail = () => {
        setShowSendEmailSuccess(true)
        dispatch(loadPromotionalEmailSendAsync(getEmailParams()))
    }

    const handleSaveDraft = () => {
        setShowSaveDraftSuccess(true)
        dispatch(loadPromotionalEmailSaveDraftAsync(getEmailParams()))
    }

    const emailReceiversLabel = useSelector(getPromotionalEmailEmailReceiversLabel)

    const emailReceiversStatus = useSelector((state) => state.promotionalEmail.emailReceiversStatus);

    const isLoadingEmailReceivers = () => emailReceiversStatus === 'loading';

    const sendEmailStatus = useSelector((state) => state.promotionalEmail.sendEmailStatus);

    const isLoadingSendEmail = () => sendEmailStatus === 'loading';

    const sendEmailSuccess = useSelector((state) => state.promotionalEmail.sendEmailSuccess);

    const [showSendEmailSuccess, setShowSendEmailSuccess] = React.useState(false);

    const saveDraftStatus = useSelector((state) => state.promotionalEmail.saveDraftStatus);

    const isLoadingSaveDraft = () => saveDraftStatus === 'loading';

    const saveDraftSuccess = useSelector((state) => state.promotionalEmail.saveDraftSuccess);

    const [showSaveDraftSuccess, setShowSaveDraftSuccess] = React.useState(false);

    return (
        <div>
            <Button style={{textTransform: 'none'}} onClick={handleOpen}><MailOutlineIcon/>{props.buttonTitle}</Button>
            <Modal
                open={open}
                onClose={handleClose}
                aria-labelledby="modal-modal-title"
                aria-describedby="modal-modal-description"
            >
                <Box sx={view === 0 ? stylePreview : styleSend}>
                    <Button onClick={handleClose} sx={{
                        position: 'absolute',
                        right: 0,
                        top: 0,
                        p: 0,
                        fontSize: 30,
                        zIndex: '1201'
                    }}>&times;</Button>
                    <Grid container spacing={2} sx={{height: '100%', mt: 0, ml: 0, width: '100%', overflow: 'auto'}}
                          style={{display: view === 0 ? 'flex' : 'none'}}>
                        <Grid item xs={6} sx={{bgcolor: '#fff'}}>
                            <Typography id="modal-modal-title" variant="h6" component="h2">
                                {t('Set a promotional e-mail')}
                            </Typography>
                            <TemplateTabs
                                typeEmail={typeEmail}
                                handleTypeEmail={handleTypeEmail}
                                htmlContent={htmlContent}
                                handleHtmlContent={handleHtmlContent}
                                plainContent={plainContent}
                                handlePlainContent={handlePlainContent}
                                coupon={coupon}
                                setCoupon={setCoupon}
                            />
                        </Grid>
                        <Grid item xs={6} sx={{bgcolor: '#f2f2f2'}}>
                            <div class="preview-wrapper">
                                <Typography id="modal-modal-title" variant="h6" component="h2">
                                    {t('Preview')}
                                </Typography>
                                <div id="preview" class={'email-preview ' + (typeEmail === 0 ? 'html' : 'plain')}>
                                    <Backdrop sx={{position: 'absolute', backgroundColor: 'inherit'}}
                                              open={isLoading()}>
                                        <CircularProgress/>
                                    </Backdrop>
                                    <div class="email-preview-wrapper" style={{display: isLoading() ? 'none' : 'block'}}
                                         dangerouslySetInnerHTML={{__html: emailPreview}}></div>
                                </div>
                                <div class="preview-actions">
                                    <Button onClick={handleSaveDraft}>{t('Save draft >')}</Button><span
                                    class="save-draft-button">{isLoadingSaveDraft() ? <CircularProgress size={20}
                                                                                                        sx={{mt: '10px'}}/> : (saveDraftSuccess && showSaveDraftSuccess ?
                                    <Alert severity="success"
                                           sx={{p: '0 16px'}}>{t('Saved draft')}</Alert> : '')}</span>
                                    <Button variant="contained" onClick={handleSendEmailView}>{t('Continue')}</Button>
                                </div>
                            </div>
                        </Grid>
                    </Grid>
                    <Grid container spacing={2} sx={{height: '100%', mt: 0, ml: 0, width: '100%', overflow: 'auto'}}
                          style={{display: view === 1 ? 'flex' : 'none'}}>
                        <Grid item xs={12} sx={{bgcolor: '#fff'}}>
                            <Button onClick={handlePreviewEmailView}>{t('> Back')}</Button>
                            <div class="send-email-wrapper">
                                <Typography id="modal-modal-title" variant="h6" component="h2" sx={{mt: '20px'}}>
                                    {t('Ready to send ?')}
                                </Typography>
                                <div style={{flexGrow: 2}}>
                                    <p style={{marginTop: 30}}>
                                        <strong>
                                            {t("You're about to send this promotional email to ")}
                                            {isLoadingEmailReceivers() ? <span
                                                style={{color: '#ccc'}}>{t('calculating... users.')}</span> : emailReceiversLabel}
                                        </strong>
                                    </p>
                                    {isLoadingSendEmail() ? <div class="send-email-spinner"><CircularProgress/>
                                    </div> : (sendEmailSuccess && showSendEmailSuccess ? <Alert severity="success"
                                                                                                sx={{mt: '30px'}}>{t('Email successfully sent')}</Alert> : '')}
                                </div>
                                <div class="send-email-button-wrapper"><Button variant="contained" sx={{width: '100%'}}
                                                                               onClick={handleSendEmail}><SendIcon/>&nbsp;{t('Send email')}
                                </Button></div>
                            </div>
                        </Grid>
                    </Grid>
                </Box>
            </Modal>
        </div>
    );
}

export default PromotionalEmailModal;
