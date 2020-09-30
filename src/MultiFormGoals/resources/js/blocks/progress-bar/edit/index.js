/**
 * WordPress dependencies
 */
const { Fragment } = wp.element;

/**
 * Internal dependencies
 */
import Inspector from './inspector';

/**
 * Vendor dependencies
 */
import ServerSideRender from '../../../components/server-side-render-x';

/**
 * Render Block UI For Editor
 */

const ProgressBar = ( { attributes, setAttributes } ) => {
	return (
		<Fragment>
			<Inspector { ... { attributes, setAttributes } } />
			<ServerSideRender block="give/progress-bar" attributes={ attributes } />
		</Fragment>
	);
};

export default ProgressBar;
