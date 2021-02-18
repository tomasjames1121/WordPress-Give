export const setSubscriptions = ( subscriptions ) => {
	return {
		type: 'SET_SUBSCRIPTIONS',
		payload: {
			subscriptions,
		},
	};
};

export const setQuerying = ( querying ) => {
	return {
		type: 'SET_QUERYING',
		payload: {
			querying,
		},
	};
};
