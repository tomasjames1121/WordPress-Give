import crosshairPlugin from './crosshair';

// Format data from Reports API for ChartJS
export function formatData( type, data ) {
	const formattedLabels = data.labels ? data.labels.slice( 0 ) : null;

	const formattedDatasets = data.datasets.map( ( dataset, index ) => {
		// Setup styles
		const styles = createStyles( type, dataset.data, index );

		const formatted = {
			data: dataset.data.slice( 0 ),
			yAxisID: `y-axis-${ index }`,
			backgroundColor: styles.backgroundColor,
			borderColor: styles.borderColor,
			borderWidth: styles.borderWidth,
		};

		return formatted;
	} );

	const formattedData = {
		labels: formattedLabels,
		datasets: formattedDatasets,
	};

	return formattedData;
}

// Create chart styles from predifined pallette,
// depending on chart type
function createStyles( type, data, index ) {
	const palette = [
		'#69B868',
		'#556E79',
		'#9EA3A8',
		'#D75A4B',
		'#F49420',
	];

	const styles = {
		backgroundColor: palette,
		borderColor: palette,
		borderWidth: 0,
	};

	// Handle special styles needed for 'line' and 'doughnut' charts
	switch ( type ) {
		case 'line':
			styles.backgroundColor = [
				palette[ index ] + '44',
			];
			styles.borderColor = [
				palette[ index ],
			];
			styles.borderWidth = 3;
			break;
		case 'doughnut':
			styles.borderColor = [ '#FFFFFF' ];
			styles.borderWidth = 3;
	}

	return styles;
}

// Return config object for ChartJS
export function createConfig( type, data ) {
	const formattedData = formatData( type, data );
	const config = {
		type: type,
		data: formattedData,
		options: {
			hover: {
				intersect: false,
			},
			legend: {
				display: false,
			},
			layout: {
				padding: 16,
			},
			scales: {
				xAxes: [],
				yAxes: [],
			},
			elements: {
				point: {
					radius: 4,
					hitRadius: 2,
					hoverRadius: 6,
					backgroundColor: '#69B868',
				},
			},
			tooltips: {
				// Disable the on-canvas tooltip
				enabled: false,
				mode: 'index',
				intersect: false,
				custom: function( tooltipModel ) {
					// Tooltip Element
					let tooltipEl = document.getElementById( 'givewp-chartjs-tooltip' );

					// Create element on first render
					if ( ! tooltipEl ) {
						tooltipEl = document.createElement( 'div' );
						tooltipEl.id = 'givewp-chartjs-tooltip';
						tooltipEl.innerHTML = '<div class="givewp-tooltip-header"></div><div class="givewp-tooltip-body"><bold></b><br></div><div class="givewp-tooltip-caret"></div>';
						document.body.appendChild( tooltipEl );
					}

					// Hide if no tooltip
					if ( tooltipModel.opacity === 0 ) {
						tooltipEl.style.opacity = 0;
						return;
					}

					// Set caret Position
					tooltipEl.classList.remove( 'above', 'below', 'no-transform' );
					if ( tooltipModel.yAlign ) {
						tooltipEl.classList.add( tooltipModel.yAlign );
					} else {
						tooltipEl.classList.add( 'no-transform' );
					}

					// `this` will be the overall tooltip
					const position = this._chart.canvas.getBoundingClientRect();

					// Display, position, and set styles for font
					tooltipEl.style.opacity = 1;
					tooltipEl.style.position = 'absolute';

					tooltipEl.style.left = position.left + tooltipModel.caretX + 'px';
					tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY - ( tooltipEl.offsetHeight + 6 ) + 'px';

					tooltipEl.style.pointerEvents = 'none';

					const tooltip = data.datasets[ tooltipModel.dataPoints[ 0 ].datasetIndex ].tooltips[ tooltipModel.dataPoints[ 0 ].index ];

					// Set tooltip inner HTML
					tooltipEl.innerHTML = `<div class="givewp-tooltip-header">${ tooltip.title }</div><div class="givewp-tooltip-body"><bold>${ tooltip.body }</b><br>${ tooltip.footer }</div><div class="givewp-tooltip-caret"></div>`;
				},
			},
		},
	};

	// Setup yAxes to begin at zero if chart is 'line' or 'bar'
	if ( type === 'line' || type === 'bar' ) {
		const yAxes = data.datasets.map( ( dataset, index ) => {
			return {
				gridLines: {
					color: '#D8D8D8',
				},
				id: `y-axis-${ index }`,
				ticks: {
					beginAtZero: true,
				},
			};
		} );

		config.options.scales = {
			yAxes: yAxes,
			xAxes: [],
		};

		if ( type === 'line' ) {
			config.options.scales.xAxes = [ {
				gridLines: {
					color: '#FFF',
				},
				type: 'time',
				time: {
					stepSize: 3,
				},
			} ];

			config.plugins = [ crosshairPlugin ];
		}
	}

	return config;
}
