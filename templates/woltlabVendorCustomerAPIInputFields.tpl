{if !$apiKeyDescriptionLanguageVariableName|isset || $apiKeyDescriptionLanguageVariableName|empty}
	{assign var=apiKeyDescriptionLanguageVariableName value='wcf.global.form.woltlabVendorCustomerAPI.customer.apiKey.description'}
{/if}
{if !$woltlabIDDescriptionLanguageVariableName|isset || $woltlabIDDescriptionLanguageVariableName|empty}
	{assign var=woltLabIDDescriptionLanguageVariableName value='wcf.global.form.woltlabVendorCustomerAPI.customer.apiKey.description'}
{/if}

<dl{if $errorField == 'apiKey'} class="formError"{/if}>
	<dt><label for="apiKey">{lang}wcf.global.form.woltlabVendorCustomerAPI.customer.apiKey{/lang}</label></dt>
	<dd>
		<input type="text" id="apiKey" name="apiKey" value="" required="required" maxlength="255" class="medium" />
		{if $errorField == 'apiKey'}
			<small class="innerError">
				{if $errorType == 'empty'}
					{lang}wcf.global.form.error.empty{/lang}
				{else}
					{lang}wcf.global.form.woltlabVendorCustomerAPI.customer.apiKey.error.{@$errorType}{/lang}
				{/if}
			</small>
		{/if}
		{hascontent}<small>{content}{lang __optional=true}{$apiKeyDescriptionLanguageVariableName}{/lang}{/content}</small>{/hascontent}
	</dd>
</dl>

<dl{if $errorField == 'woltlabID'} class="formError"{/if}>
	<dt><label for="woltlabID">{lang}wcf.global.form.woltlabVendorCustomerAPI.customer.woltlabID{/lang}</label></dt>
	<dd>
		<input type="text" id="woltlabID" name="woltlabID" value="" required="required" maxlength="255" class="medium" />
		{if $errorField == 'woltlabID'}
			<small class="innerError">
				{if $errorType == 'empty'}
					{lang}wcf.global.form.error.empty{/lang}
				{else}
					{lang}wcf.global.form.woltlabVendorCustomerAPI.customer.woltlabID.error.{@$errorType}{/lang}
				{/if}
			</small>
		{/if}
		{hascontent}<small>{content}{lang __optional=true}{$woltLabIDDescriptionLanguageVariableName}{/lang}{/content}</small>{/hascontent}
	</dd>
</dl>