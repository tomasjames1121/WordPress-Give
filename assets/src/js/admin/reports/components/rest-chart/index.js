// Vendor dependencies
import axios from 'axios';
import { useState, useEffect, Fragment } from 'react';
import PropTypes from 'prop-types';

// Components
import Chart from '../chart';
import SkeletonChart from '../skeleton-chart';
import Spinner from '../spinner';

// Store-related dependencies
import { useStoreValue } from '../../store';
import { setGiveStatus, setPageLoaded } from '../../store/actions';

const RESTChart = ( { title, type, aspectRatio, endpoint, showLegend, headerElements } ) => {
	// Use period from store
	const [ { period, giveStatus }, dispatch ] = useStoreValue();

	// Use state to hold data fetched from API
	const [ fetched, setFetched ] = useState( null );

	// Use to manage loading state
	const [ loaded, setLoaded ] = useState( false );

	const [ querying, setQuerying ] = useState( false );

	const CancelToken = axios.CancelToken;
	const source = CancelToken.source();

	// Fetch new data and update Chart when period changes
	useEffect( () => {
		if ( period.startDate && period.endDate ) {
			if ( querying === true ) {
				source.cancel( 'Operation canceled by the user.' );
			}

			setQuerying( true );
			setLoaded( false );

			axios.get( wpApiSettings.root + 'give-api/v2/reports/' + endpoint, {
				cancelToken: source.token,
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
					setLoaded( true );
					setFetched( response.data.data );
					if ( endpoint === 'income' ) {
						const status = response.data.data.status;
						dispatch( setGiveStatus( status ) );
						dispatch( setPageLoaded() );
					}
				} )
				.catch( function() {
					setQuerying( false );
				} );
		}
	}, [ period, endpoint ] );

	const ready = giveStatus === 'donations_found' && fetched !== null ? true : false;

	return (
		<Fragment>
			{ title && (
				<div className="givewp-chart-title">
					<span className="givewp-chart-title-text">{ title }</span>
					{ ! loaded && (
						<Spinner />
					) }
					{ headerElements && (
						headerElements
					) }
				</div>
			) }
			{ ready ? (
				<Chart
					type={ type }
					aspectRatio={ aspectRatio }
					data={ fetched }
					showLegend={ showLegend }
				/>
			) : (
				<SkeletonChart
					type={ type }
					aspectRatio={ aspectRatio }
					showLegend={ showLegend }
				/>
			) }
		</Fragment>
	);
};

RESTChart.propTypes = {
	// Chart type (ex: line)
	type: PropTypes.string.isRequired,
	// Chart aspect ratio
	aspectRatio: PropTypes.number,
	// API endpoint where data is fetched (ex: 'payment-statuses')
	endpoint: PropTypes.string.isRequired,
	// Display Chart with Legend
	showLegend: PropTypes.bool,
};

RESTChart.defaultProps = {
	type: null,
	aspectRatio: 0.6,
	endpoint: null,
	showLegend: false,
};

export default RESTChart;
