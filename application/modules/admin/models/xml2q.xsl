<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="html" indent="yes"/>

	<xsl:template match="content">
		<h3 style="color:red;">Question:</h3>
		<table width="95%">
			<tbody>
				<xsl:apply-templates />
			</tbody>
		</table>
		<p>&#160;</p>
		<h3 style="color:red;">Literature:</h3>
		<table width="95%">
			<tbody>
				<tr>
					<td>
						<xsl:apply-templates select="./literature/node()"/>
					</td>
				</tr>
			</tbody>
		</table>
		<p>&#160;</p>			
		<h3 style="color:red;">Annotation:</h3>
		<table width="95%">
			<tbody>
				<tr>
					<td>
						<xsl:apply-templates select="./annotation/node()"/>
					</td>
				</tr>
			</tbody>
		</table>			
	</xsl:template>

	<xsl:template match="question">
		<tr>
			<td colspan="2"><xsl:apply-templates /></td>
		</tr>
	</xsl:template>

	<xsl:template match="answers">
		<xsl:apply-templates />
	</xsl:template>

	<xsl:template match="answer">
		<tr>
			<td>
				<xsl:if test="position()=1">
					<xsl:attribute name="width">20px</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="@weight"/>
			</td>
			<td><xsl:apply-templates /></td>
		</tr>
	</xsl:template>

	<xsl:template match="annotation|literature"/>

	<xsl:template match="@*|node()" priority="-2">
		<xsl:copy>
			<xsl:apply-templates select="@*|node()"/>
		</xsl:copy>
	</xsl:template>
	<xsl:template match="text()" priority="-1">
		<xsl:value-of select="."/>
	</xsl:template>
</xsl:stylesheet>
