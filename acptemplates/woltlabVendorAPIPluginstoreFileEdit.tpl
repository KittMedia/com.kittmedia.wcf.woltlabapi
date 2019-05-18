{include file='header' pageTitle='wcf.woltlabapi.pluginstore.file.'|concat:$action}
{include file='multipleLanguageInputJavascript' elementIdentifier='name' forceSelection=false}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{$pageTitle|language}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='WoltlabVendorAPIPluginstoreFileList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.menu.link.content.woltlabVendorAPI.pluginstoreFileList{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<form method="post" action="{link controller='WoltlabVendorAPIPluginstoreFileEdit' id=$fileID}{/link}">
	<div class="section">
		<h2 class="sectionTitle">{lang}wcf.global.form.data{/lang}</h2>
		
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
			<dt></dt>
			<dd>
				<label>
					<input type="checkbox" id="isDisabled" name="isDisabled"{if $isDisabled} checked="checked"{/if} />
					{lang}wcf.woltlabapi.pluginstore.file.isDisabled{/lang}
				</label>
				<small>{lang}wcf.woltlabapi.pluginstore.file.isDisabled.description{/lang}</small>
			</dd>
		</dl>
		
		{event name='informationFields'}
	</div>
	
	{event name='afterSections'}
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{hascontent}
	<footer class="contentFooter">
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	</footer>
{/hascontent}

{include file='footer'}
