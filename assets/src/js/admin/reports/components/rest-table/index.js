// Vendor dependencies
import axios from 'axios';
import { useState, useEffect, Fragment } from 'react';
import PropTypes from 'prop-types';

// Components
import Table from '../table';
import LoadingOverlay from '../loading-overlay';
import NotFoundOverlay from '../not-found-overlay';

// Utilities
import { getSkeletonLabels, getSkeletonRows, getLabels, getRows } from './utils';

// Store-related dependencies
import { useStoreValue } from '../../store';

const RESTTable = ( { title, endpoint } ) => {
	// Use period from store
	const [ { period, giveStatus } ] = useStoreValue();

	// Use state to hold data fetched from API
	const [ fetched, setFetched ] = useState( null );

	const [ dataFound, setDataFound ] = useState( true );

	const [ loaded, setLoaded ] = useState( false );

	const [ querying, setQuerying ] = useState( false );

	const CancelToken = axios.CancelToken;
	const source = CancelToken.source();

	// Fetch new data and update List when period changes
	useEffect( () => {
		if ( period.startDate && period.endDate ) {
			if ( querying === true ) {
				source.cancel( 'Operation canceled by the user.' );
			}

			setQuerying( true );
			setLoaded( false );

			axios.get( wpApiSettings.root + 'give-api/v2/reports/' + endpoint, {
				params: {
					start: period.startDate.format( 'YYYY-MM-DD-HH' ),
					end: period.endDate.format( 'YYYY-MM-DD-HH' ),
				},
				headers: {
					'X-WP-Nonce': wpApiSettings.nonce,
				},
			} )
				.then( function( response ) {
					setQuerying( false );
					setFetched( response.data.data );
					const found = response.data.data.length > 0 ? true : false;
					setDataFound( found );
					setLoaded( true );
				} )
				.catch( function() {
					setQuerying( false );
				} );
		}
	}, [ period, endpoint ] );

	const ready = giveStatus === 'donations_found_for_period' ? true : false;

	let labels;
	let rows;

	if ( ready ) {
		labels = getLabels( fetched );
		rows = getRows( fetched );
	} else {
		labels = getSkeletonLabels( fetched );
		rows = getSkeletonRows( fetched );
	}

	let overlay;
	switch ( true ) {
		case loaded === false: {
			overlay = <LoadingOverlay />;
			break;
		}
		case dataFound === false: {
			overlay = <NotFoundOverlay />;
			break;
		}
	}

	return (
		<Fragment>
			{ overlay }
			<Table
				title={ title }
				labels={ labels }
				rows={ rows }
			/>
		</Fragment>
	);
};

RESTTable.propTypes = {
	// API endpoint where data is fetched (ex: 'payment-statuses')
	endpoint: PropTypes.string.isRequired,
};

RESTTable.defaultProps = {
	endpoint: null,
};

export default RESTTable;
