<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;
use X2Y\HyperSwitchClient\Enum\TransactionStatus;

/**
 * @openapi             "Initiate external authentication for a Payment"
 * @endpoint            POST /payments/{id}/3ds/authentication
 * @hyperswitch-source  2026-08-19 (openapi_spec_v1 @ ada4a1e)
 */
final class ExternalAuthenticationResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly TransactionStatus $transStatus,
        public readonly string $threeDsRequestorUrl,
        public readonly ?string $acsUrl = null,
        public readonly ?string $challengeRequest = null,
        public readonly ?string $challengeRequestKey = null,
        public readonly ?string $acsReferenceNumber = null,
        public readonly ?string $acsTransId = null,
        public readonly ?string $threeDsserverTransId = null,
        public readonly ?string $acsSignedContent = null,
        public readonly ?string $threeDsRequestorAppUrl = null,
        public readonly ?string $errorMessage = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = [
            'trans_status', 'three_ds_requestor_url', 'acs_url', 'challenge_request',
            'challenge_request_key', 'acs_reference_number', 'acs_trans_id',
            'three_dsserver_trans_id', 'acs_signed_content', 'three_ds_requestor_app_url',
            'error_message',
        ];

        return new self(
            transStatus:            TransactionStatus::from((string) $data['trans_status']),
            threeDsRequestorUrl:    (string) $data['three_ds_requestor_url'],
            acsUrl:                 isset($data['acs_url'])                    ? (string) $data['acs_url']                    : null,
            challengeRequest:       isset($data['challenge_request'])         ? (string) $data['challenge_request']         : null,
            challengeRequestKey:    isset($data['challenge_request_key'])     ? (string) $data['challenge_request_key']     : null,
            acsReferenceNumber:     isset($data['acs_reference_number'])      ? (string) $data['acs_reference_number']      : null,
            acsTransId:             isset($data['acs_trans_id'])              ? (string) $data['acs_trans_id']              : null,
            threeDsserverTransId:   isset($data['three_dsserver_trans_id'])   ? (string) $data['three_dsserver_trans_id']   : null,
            acsSignedContent:       isset($data['acs_signed_content'])        ? (string) $data['acs_signed_content']        : null,
            threeDsRequestorAppUrl: isset($data['three_ds_requestor_app_url'])? (string) $data['three_ds_requestor_app_url']: null,
            errorMessage:           isset($data['error_message'])             ? (string) $data['error_message']             : null,
            extras:                 array_diff_key($data, array_flip($known)),
        );
    }
}
