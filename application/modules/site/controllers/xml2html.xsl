<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">

<xsl:param name="urlBase" />
<xsl:param name="nodeResPath" />

<xsl:output method="xml" omit-xml-declaration="yes" indent="no"/>

	<xsl:template match="content">
		<!-- CONTENT -->
		<xsl:choose>
			<xsl:when test="not (.//div[@class='article']) and not (@noarticle)">
				<xsl:call-template name="article" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates />
			</xsl:otherwise>
		</xsl:choose>
		<!-- /CONTENT -->
	</xsl:template>


	<xsl:template match="title | h1" />
	<xsl:template match="div[@class='subarticle']/h1 | div[@class='subarticle']/h2[@class='artitle']" />

	<xsl:template match="h1 | h2" mode="show_title">
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template name="article">
		<xsl:param name="subarticle">0</xsl:param>
		<table class="article" style="width: 100%; height: 100%">
			<tr>
				<td class="sep1">
					<div />
				</td>
			</tr>
			<tr style="height: 25px"> 
				<td>
					<xsl:choose>
						<xsl:when test="$subarticle=1">
							<h2 class="artitle">
								<xsl:apply-templates select=".//h2[@class='artitle']"
									mode="show_title" />
							</h2>
						</xsl:when>
						<xsl:otherwise>
							<h1>
								<xsl:apply-templates select=".//h1"
									mode="show_title" />
							</h1>
						</xsl:otherwise>
					</xsl:choose>
				</td>
			</tr>
			<tr>
				<td class="cnt">
					<div>
						<xsl:apply-templates />
					</div>
				</td>
			</tr>
			<tr>
				<td class="sep2">
					<div />
				</td>
			</tr>
		</table>
	</xsl:template>

	<xsl:template match="div[@class='subarticle']">
		<xsl:call-template name="article">
			<xsl:with-param name="subarticle">1</xsl:with-param>
		</xsl:call-template>
	</xsl:template>

	<xsl:template match="div[@class='article']">
		<xsl:call-template name="article"/>
	</xsl:template>

	<xsl:template match="html|body">
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="head" />

	<xsl:template match="img|IMG">
		<xsl:copy>
			<xsl:apply-templates select="@*" />
			<xsl:if test="not(starts-with(@src, 'http:'))"><!-- and not(starts-with(@src, $urlBase))">-->
				<xsl:attribute name="src">
          <xsl:value-of select="$nodeResPath" /><xsl:value-of
					select="@src" /><xsl:value-of select="@SRC" />
        </xsl:attribute>
			</xsl:if>
		</xsl:copy>
	</xsl:template>

	<xsl:template match="*[@background]">
		<xsl:copy>
			<xsl:apply-templates select="@*" />
			<xsl:if test="not(starts-with(@background, 'http:'))">
				<xsl:attribute name="background">
          <xsl:value-of select="$nodeResPath" /><xsl:value-of
					select="@background" />
        </xsl:attribute>
			</xsl:if>
		</xsl:copy>
	</xsl:template>


	<xsl:template match="a">
		<xsl:copy>
			<xsl:apply-templates select="@*" />
			<xsl:if test="starts-with(@href, 'downloads/')">
				<xsl:attribute name="href">
					 <xsl:value-of select="$nodeResPath" /><xsl:value-of	select="substring-after(@href, 'downloads/')" />
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="starts-with(@href, 'http://') and not(contains(@href, 'csmu.edu.ua')) and not (@rel)">
				<xsl:attribute name="rel">nofollow</xsl:attribute>
			</xsl:if>
			<xsl:apply-templates select="node()" />
		</xsl:copy>
	</xsl:template>


	<xsl:template match="base" />


	<xsl:template match="div[@class='widget']">
		<xsl:if test="@params">
			<xsl:value-of
				select="php:function('TypecontentController::widget', string(@params))"
				disable-output-escaping="yes" />
		</xsl:if>
	</xsl:template>

	<xsl:template match="@ilo-ph-fix" />

	<xsl:template match="@*|node()" priority="-2">
		<xsl:copy>
			<xsl:apply-templates select="@*|node()" />
		</xsl:copy>
	</xsl:template>

	<xsl:template match="text()" priority="-1">
		<xsl:value-of select="." />
	</xsl:template>

</xsl:stylesheet>