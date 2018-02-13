/**
* Block dependencies
*/
import GiveHelpLink from '../help-link/index';

/**
* Internal dependencies
*/
const { __ } = wp.i18n;

const GiveBlankSlate = (props) => {

	const {
			noIcon,
			isLoader,
			title,
			description,
			children,
			helpLink
		} = props;

	const giveLoader = '/wp-content/plugins/Give/assets/dist/images/give-loader.svg';
	const giveLogo = '/wp-content/plugins/Give/assets/dist/images/give-icon-full-circle.svg';

	return (
		<div class="give-blank-slate">
			{ !noIcon && (
				<img class="give-blank-slate__image"
					src={ `${wpApiSettings.schema.url}${!!isLoader ? giveLoader : giveLogo }` }
					alt={ __( "Give Icon" ) } />
			) }

			{ !!title && ( <h2 class="give-blank-slate__heading">{ title }</h2> ) }
			{ !!description && (<p class="give-blank-slate__message">{ description }</p>)}
			{ children }
			{ ( !!helpLink && <GiveHelpLink/> ) }
		</div>
	)
}

export default GiveBlankSlate;
