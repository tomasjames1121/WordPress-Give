// Import vendor dependencies
import { useState, useEffect } from 'react';
const { __ } = wp.i18n;

// Import utilities
import { getWindowData } from '../../utils';

// Import components
import ConfigurationIcon from '../icons/configuration';

// Import styles
import './style.scss';

const DonationForm = () => {
	const formPreviewUrl = getWindowData( 'formPreviewUrl' );
	const [ iframeLoaded, setIframeLoaded ] = useState( false );
	const [ iframeHeight, setIframeHeight ] = useState( 749 );

	useEffect( () => {
		window.addEventListener( 'message', receiveMessage, false );
		return () => {
			window.removeEventListener( 'message', receiveMessage, false );
		};
	}, [] );

	const receiveMessage = ( event ) => {
		setIframeHeight( event.data.payload.height );
	};

	const iframeStyle = {
		height: iframeHeight,
		opacity: iframeLoaded === false ? '0' : '1',
	};
	const messageStyle = {
		height: iframeHeight,
		opacity: iframeLoaded === false ? '1' : '0',
	};

	const onIframeLoaded = () => {
		setIframeLoaded( true );

		document.getElementById( 'donationFormPreview' ).contentDocument
			.getElementById( 'iFrameResizer0' ).contentDocument
			.getElementById( 'give_error_test_mode' )
			.style.display = 'none';
	};

	return (
		<div className="give-obw-donation-form-preview">
			<div className="give-obw-donation-form-preview__loading-message" style={ messageStyle }>
				<ConfigurationIcon />
				<h3>
					{ __( 'Building Form Preview...', 'give' ) }
				</h3>
			</div>
			<iframe id="donationFormPreview" onLoad={ onIframeLoaded } className="give-obw-donation-form-preview__iframe" scrolling="no" src={ formPreviewUrl } style={ iframeStyle } />
		</div>
	);
};

export default DonationForm;
