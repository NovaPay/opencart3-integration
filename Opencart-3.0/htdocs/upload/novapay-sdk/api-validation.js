const validateSession = validation.compile({
	properties: {
		client_first_name: { type: ['string', 'null'], minLength: 0, maxLength: 255, default: '' },
		client_last_name: { type: ['string', 'null'], minLength: 0, maxLength: 255, default: '' },
		client_patronymic: { type: ['string', 'null'], minLength: 0, maxLength: 255, default: '' },
		client_phone: { type: 'string', minLength: 1, maxLength: 255, validPhone: true },
		client_email: { type: ['string', 'null'], format: 'email', minLength: 0, maxLength: 255 },
		metadata: { type: ['null', 'object'] },
		callback_url: { type: ['string', 'null'], format: 'uri', minLength: 0, maxLength: 255 },
		success_url: { type: ['string', 'null'], format: 'uri', minLength: 0, maxLength: 255 },
		fail_url: { type: ['string', 'null'], format: 'uri', minLength: 0, maxLength: 255 },
	},
	additionalProperties: false,
	required: [
		'client_phone'
	]
});

const validatePayment = validation.compile({
	properties: {
		session_id: { type: 'string', minLength: 1, maxLength: 255 },
		amount: { type: 'number', minimum: 0.01, validAmount: true },
		external_id: { type: ['null', 'string'], maxLength: 255 },
		products: {
			type: ['array', 'null'], items: {
				type: 'object',
				properties: {
					description: { type: ['string', 'null'] },
					count: { type: ['number', 'null'], minimum: 1 },
					price: { type: ['number', 'null'], minimum: 0.01, validAmount: true }
				},
				additionalProperties: false
			}
		},
		use_hold: { type: 'boolean', default: false }
	},
	additionalProperties: false,
	required: ['session_id', 'amount']
});