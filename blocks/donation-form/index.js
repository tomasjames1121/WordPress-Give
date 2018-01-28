/**
 * Block dependencies
 */
import './style.scss';

const { __ } = wp.i18n;
const {
	registerBlockType,
	InspectorControls,
	BlockDescription,
} = wp.blocks;
const {
	ToggleControl,
	SelectControl,
	TextControl,
} = InspectorControls;
const {
	PanelBody,
	Button,
} = wp.components;

export default registerBlockType( 'give/donation-form', {

	title: __( 'Give Donation Form' ),
	category: 'common',
	supportHTML: false,

	attributes: {
		id: {
			type: 'number',
		},
		displayStyle: {
			type: 'string',
		},
		continueButtonTitle: {
			type: 'string',
		},
		showTitle: {
			type: 'boolean',
			default: false,
		},
		showGoal: {
			type: 'boolean',
			default: false,
		},
		contentDisplay: {
			type: 'boolean',
			default: false,
		},
		showContent: {
			type: 'string',
			default: 'none',
		},
	},

	edit: props => {
		const attributes = props.attributes;

		const displayStyles = [
			{ value: 'onpage', label: 'Full Form' },
			{ value: 'modal', label: 'Modal' },
			{ value: 'reveal', label: 'Reveal' },
			{ value: 'button', label: 'One-button Launch' },
		];

		const contentPosition = [
			{ value: 'above', label: 'Above' },
			{ value: 'below', label: 'Below' },
		];

		const formContentPlacement = [
			{ above: 'give_pre_form' },
			{ below: 'give_post_form' },
			{ none: '' },
		];

		const loadFormData = id => {
			window.fetch( `${ wpApiSettings.schema.url }/wp-json/give-api/v1/form/${ id }` ).then(
				( response ) => {
					response.json().then( ( reply ) => {
						props.setAttributes( { form: reply } );
					} );
				}
			);
		};

		const getFormOptions = () => {
			const formOptions = attributes.forms.map( ( form ) => {
				return {
					value: form.info.id,
					label: form.info.title,
				};
			} );

			// Default option
			formOptions.unshift( { value: '-1', label: 'Select a Donation Form...' } );

			return formOptions;
		};

		const setFormIdTo = id => {
			props.setAttributes( { id: id } );
			loadFormData( id );
		};

		const setDisplayStyleTo = format => {
			props.setAttributes( { displayStyle: format } );
		};

		const setContinueButtonTitle = buttonTitle => {
			props.setAttributes( { continueButtonTitle: buttonTitle } );
		};

		const toggleShowTitle = () => {
			props.setAttributes( { showTitle: ! attributes.showTitle } );
		};

		const toggleShowGoal = () => {
			props.setAttributes( { showGoal: ! attributes.showGoal } );
		};

		const toggleContentDisplay = () => {
			props.setAttributes( { contentDisplay: ! attributes.contentDisplay } );

			// Set form Content Display Position
			if ( ! attributes.contentDisplay ) {
				props.setAttributes( { showContent: 'above' } ); // true && above
			} else if ( !! attributes.contentDisplay ) {
				props.setAttributes( { showContent: 'none' } ); // false && none
			}
		};

		const setShowContentPosition = position => {
			props.setAttributes( { showContent: position } );
		};

		const inspectorControls = (
			<InspectorControls key="inspector">
				<BlockDescription>
					<p>{ __( 'The Give Donation Form block insert an existing donation form into the page. Each form\'s presentation can be customized below.' ) }</p>
				</BlockDescription>
				<PanelBody title={ __( 'Presentation' ) }>
					<SelectControl
						label={ __( 'Format' ) }
						value={ attributes.displayStyle }
						options={ displayStyles }
						onChange={ setDisplayStyleTo }
					/>
					{
						'reveal' === attributes.displayStyle && (
							<TextControl
								label={ __( 'Continue Button Title' ) }
								value={ attributes.continueButtonTitle }
								onChange={ setContinueButtonTitle }
							/>
						)
					}
				</PanelBody>
				<PanelBody title={ __( 'Form Components' ) }>
					<ToggleControl
						label={ __( 'Form Title' ) }
						checked={ !! attributes.showTitle }
						onChange={ toggleShowTitle }
					/>
					<ToggleControl
						label={ __( 'Form Goal' ) }
						checked={ !! attributes.showGoal }
						onChange={ toggleShowGoal }
					/>
					<ToggleControl
						label={ __( 'Form Content' ) }
						checked={ !! attributes.contentDisplay }
						onChange={ toggleContentDisplay }
					/>
					{
						attributes.contentDisplay && (
							<SelectControl
								label={ __( 'Content Position' ) }
								value={ attributes.showContent }
								options={ contentPosition }
								onChange={ setShowContentPosition }
							/>
						)
					}
				</PanelBody>
			</InspectorControls>
		);

		if ( ! attributes.id && ! attributes.forms ) {
			window.fetch( `${ wpApiSettings.schema.url }/give-api/forms/?key=${ window.give_blocks_vars.key }&token=${ window.give_blocks_vars.token }` ).then(
				( response ) => {
					response.json().then( ( reply ) => {
						props.setAttributes( { forms: reply.forms } );
					} );
				}
			);

			return 'loading !';
		}

		if ( ! attributes.id && attributes.forms.length === 0 ) {
			return 'No forms';
		}

		if ( ! attributes.id ) {
			return (
				<div>
					<SelectControl
						label={ __( 'Give Donation Form' ) }
						options={ getFormOptions() }
						onChange={ setFormIdTo }
					/>
				</div>
			);
		}

		if ( ! attributes.form ) {
			loadFormData( attributes.id );
			return 'loading !';
		}

		return (
			<div>
				{ !! props.focus && inspectorControls }
				<div id={ `give-form-${ attributes.id }` }>
					<form action="" className={ `give-form give-form-${ attributes.id } give-form-type-multi` }>
						{
							attributes.showTitle && (
								<h2 className="give-form-title">{ attributes.form.title }</h2>
							)
						}

						{
							'above' === attributes.showContent && (
								<div
									id={ `give-form-content-${ attributes.id }` }
									className={ `give-form-content-wrap ${ formContentPlacement[ attributes.showContent ] }-content` }
									dangerouslySetInnerHTML={ { __html: attributes.form.content } }
								/>
							)
						}

						<div id="give_purchase_form_wrap">
							<fieldset id="give_checkout_user_info">
								<legend>{ __( 'Personal Info', 'give' ) }</legend>
								<p id="give-first-name-wrap" className="form-row form-row-first form-row-responsive">
									<label className="give-label" htmlFor="give-first">
										{ __( 'First Name', 'give' ) }
									</label>
									<TextControl
										className="give-input required"
										type="text"
										name="give_first"
										placeholder={ __( 'First Name', 'give' ) }
										id="give-first"
									/>
								</p>
								<p id="give-last-name-wrap" className="form-row form-row-last form-row-responsive">
									<label className="give-label" htmlFor="give-last">
										{ __( 'Last Name', 'give' ) }
									</label>
									<TextControl
										className="give-input"
										type="text"
										name="give_last"
										id="give-last"
										placeholder={ __( 'Last Name', 'give' ) }
									/>
								</p>
								<p id="give-email-wrap" className="form-row form-row-wide">
									<label className="give-label" htmlFor="give-email">
										{ __( 'Email Address', 'give' ) }
									</label>
									<TextControl
										className="give-input required"
										type="email"
										name="give_email"
										placeholder={ __( 'Email Address', 'give' ) }
										id="give-email"
									/>
								</p>
							</fieldset>
							<fieldset id="give_purchase_submit">
								<div className="give-submit-button-wrap give-clearfix">
									<p id="give-final-total-wrap" className="form-wrap">
										<span className="give-donation-total-label">
											{ __( 'Donation Total:', 'give' ) }
										</span>
										<span
											className="give-final-total-amount"
											data-total={ attributes.form.format_amount }
											dangerouslySetInnerHTML={ { __html: attributes.form.final_amount } }
										/>
									</p>
									<Button
										type="button"
										className="give-submit give-btn"
										id="give-purchase-button"
										name="give-purchase" >
										{ __( 'Donate Now', 'give' ) }
									</Button>
								</div>
							</fieldset>
						</div>
					</form>
				</div>
			</div>
		);
	},

	save: () => {
		return null;
	},
} );
