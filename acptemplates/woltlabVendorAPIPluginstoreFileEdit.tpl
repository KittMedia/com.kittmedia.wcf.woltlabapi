{include file='header' pageTitle='wcf.woltlabapi.pluginstore.file.'|concat:$action}
{include file='multipleLanguageInputJavascript' elementIdentifier='name' forceSelection=false}

<header class="boxHeadline">
	<h1>{lang}wcf.woltlabapi.pluginstore.file.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='WoltlabVendorAPIPluginstoreFileList'}{/link}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}wcf.acp.menu.link.content.woltlabVendorAPI.pluginstoreFileList{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{link controller='WoltlabVendorAPIPluginstoreFileEdit' id=$fileID}{/link}">
	<div class="container containerPadding marginTop">
		{event name='beforeFieldsets'}
		
		<fieldset>
			<legend>{lang}wcf.global.form.data{/lang}</legend>
			
			<dl{if $errorField == 'name'} class="formError"{/if}>
				<dt><label for="name">{lang}wcf.woltlabapi.pluginstore.file.name{/lang}</label></dt>
				<dd>
					<input type="text" id="name" name="name" required="required" value="{$i18nPlainValues['name']}" class="long" />
					{if $errorField == 'name'}
						<small class="innerError">
							{if $errorType == 'empty' || $errorType == 'multilingual'}
								{lang}wcf.global.form.error.{$errorType}{/lang}
							{else}
								{lang}wcf.woltlabapi.pluginstore.file.name.error.{$errorType}{/lang}
							{/if}
						</small>
					{/if}
					<small>{lang}wcf.woltlabapi.pluginstore.file.name.description{/lang}</small>
				</dd>
			</dl>
			
			<dl>
				<dt class="reversed"><label for="isDisabled">{lang}wcf.woltlabapi.pluginstore.file.isDisabled{/lang}</label></dt>
				<dd>
					<input type="checkbox" id="isDisabled" name="isDisabled"{if $isDisabled} checked="checked"{/if} />
					<small>{lang}wcf.woltlabapi.pluginstore.file.isDisabled.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='dataFields'}
		</fieldset>
		
		{event name='afterFieldsets'}
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}