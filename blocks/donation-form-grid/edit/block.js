/**
 * Block dependencies
 */
import isEmpty from 'lodash.isempty';
import pickBy from 'lodash.pickby';
import isUndefined from 'lodash.isundefined';
import GiveBlankSlate from '../../components/blank-slate/index';
import NoForms from '../../components/no-form/index';
import FormGridPreview from './components/preview';
import {stringify} from 'querystringify';

/**
 * Internal dependencies
 */
const {__}          = wp.i18n,
	  {withAPIData} = wp.components,
	  {Component}   = wp.element;

/**
 * Render Block UI For Editor
 *
 * @class GiveDonationFormGrid
 * @extends {Component}
 */
class GiveDonationFormGrid extends Component {
	constructor(props) {
		super(...props);
	}

	/**
	 * Render block UI
	 *
	 * @returns {object} JSX Object
	 * @memberof GiveDonationFormGrid
	 */
	render() {
		const props         = this.props,
			  {latestForms} = props,
			  hasForms      = !isUndefined(latestForms.data) && !isEmpty(latestForms.data);

		// Render block UI
		let blockUI;

		if (latestForms.isLoading) {
			blockUI = <GiveBlankSlate title={__('Loading...')} isLoader/>;
		} else if (!hasForms) {
			blockUI = <NoForms/>;
		} else {
			blockUI = <FormGridPreview
				html={latestForms.data}
				{... {...props}} />;
		}

		return (<div className={props.className} key="GiveDonationFormGridBlockUI">{blockUI}</div>);
	}
}

/**
 * Export component attaching withAPIdata
 */
export default withAPIData((props) => {
	const {columns, showGoal, showExcerpt, showFeaturedImage, displayType} = props.attributes;
	const parameters                                                       = stringify(pickBy({
			columns: columns,
			show_goal: showGoal,
			show_excerpt: showExcerpt,
			show_featured_image: showFeaturedImage,
			display_type: displayType
		}, value => !isUndefined(value)
	));

	return {
		latestForms: `/give-api/v2/form-grid/?${ parameters }`,
	};
})(GiveDonationFormGrid);
