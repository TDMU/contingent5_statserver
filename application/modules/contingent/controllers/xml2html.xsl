<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes='php'>

<xsl:param name="contextPath" />
<xsl:param name="nodeResPath" />

<xsl:output method="html" indent="no"/>

	<xsl:template match="content">
		<xsl:choose>
			<xsl:when test="not (.//div[@class='article']) and not (@noarticle)">
				<xsl:call-template name="article" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template match="p[@class='title']|title" />

	<xsl:template match="p[@class='title']" mode="show_title">
		<xsl:apply-templates />
	</xsl:template>

  <xsl:template name="article" >
    <table style="width: 100%; height: 100%">
      <tr style="height: 25px">
        <td style="font-size:17px;font-weight:bold">
          <xsl:apply-templates select=".//p[@class='title']" mode="show_title"/>
        </td>
      </tr>
      <tr>
        <td class="cnt">
          <xsl:apply-templates />
        </td>
      </tr>
    </table>
  </xsl:template>

	<xsl:template match="div[@class='article']">
		<xsl:call-template name="article" />
	</xsl:template>

	<xsl:template match="html|body">
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="head" />

	<xsl:template match="img|IMG">
		<xsl:copy>
			<xsl:apply-templates select="@*" />
			<xsl:if test="not(starts-with(@src, 'http:'))"><!-- and not(starts-with(@src, $contextPath))">-->
				<xsl:attribute name="src">
					<xsl:value-of select="$nodeResPath" /><xsl:value-of select="@src" /><xsl:value-of select="@SRC" />
				</xsl:attribute>
			</xsl:if>
		</xsl:copy>
	</xsl:template>

	<xsl:template match="*[@background]">
		<xsl:copy>
			<xsl:apply-templates select="@*" />
			<xsl:if test="not(starts-with(@background, 'http:'))">
				<xsl:attribute name="background">
					<xsl:value-of select="$nodeResPath" /><xsl:value-of	select="@background" />
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
			<xsl:apply-templates select="node()" />
		</xsl:copy>
	</xsl:template>


	<xsl:template match="base" />


	<xsl:template match="div[@class='widget']">
		<table style="width: 100%; height: 100%">
			<tr>
				<td class="sep1">
					<div />
				</td>
			</tr>
			<tr height="25px">
				<td>
					<div class="main">
						<xsl:apply-templates select=".//text()" />
					</div>
				</td>
			</tr>
			<tr>
				<td class="cnt">
					<div>
						<xsl:if test="@src">
							<xsl:value-of select="php:function('Zend_View_Helper_XsltHelper::widget', string(@src), string(@param))" disable-output-escaping="yes" />
						</xsl:if>
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

	<xsl:template match="@*|node()" priority="-2">
		<xsl:copy>
			<xsl:apply-templates select="@*|node()" />
		</xsl:copy>
	</xsl:template>

	<xsl:template match="text()" priority="-1">
		<xsl:value-of select="." />
	</xsl:template>

</xsl:stylesheet>