<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="xml" indent="yes" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"/>

	<xsl:template match="content">
		<content>
			<question>
				<xsl:apply-templates select="table[position()=1]/tbody/tr[position()=1]/td/node()"/>
			</question>
			<answers>
				<xsl:apply-templates select="table[position()=1]/tbody/tr[position()>1]" mode="answer"/>
			</answers>
			<literature>
				<xsl:apply-templates select="table[position()=2]/tbody/tr/td/node()"/>
			</literature>
			<annotation>
				<xsl:apply-templates select="table[position()=3]/tbody/tr/td/node()"/>
			</annotation>
		</content>
	</xsl:template>

	<xsl:template match="tr" mode="answer">
		<xsl:if test="string-length(normalize-space(translate(td[position()=2]/text(), '&#160;', ''))) > 0">
			<answer>
				<xsl:attribute name="weight"><xsl:value-of select="normalize-space(td[position()=1]/text())"/></xsl:attribute>
				<xsl:apply-templates select="td[position()=2]/node()"/>
			</answer>
		</xsl:if>
	</xsl:template>

	<xsl:template match="@*|node()" priority="-2">
		<xsl:copy>
			<xsl:apply-templates select="@*|node()"/>
		</xsl:copy>
	</xsl:template>
	<xsl:template match="text()" priority="-1">
		<xsl:value-of select="."/>
	</xsl:template>
</xsl:stylesheet>
