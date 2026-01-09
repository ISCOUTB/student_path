<?xml version="1.0" encoding="UTF-8" ?>
<XMLDB PATH="blocks/student_path/db" VERSION="20220419" COMMENT="XMLDB file for block student_path"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="../../../lib/xmldb/xmldb.xsd"
>
  <TABLES>
    <TABLE NAME="block_student_path" COMMENT="Stores student path data">
      <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true"/>
        <FIELD NAME="user" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="false"/>
        <FIELD NAME="name" TYPE="char" LENGTH="255" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="program" TYPE="char" LENGTH="255" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="admission_year" TYPE="int" LENGTH="4" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="admission_semester" TYPE="int" LENGTH="1" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="email" TYPE="char" LENGTH="255" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="code" TYPE="char" LENGTH="50" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="personality_strengths" TYPE="text" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="personality_weaknesses" TYPE="text" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="vocational_areas" TYPE="char" LENGTH="50" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="vocational_areas_secondary" TYPE="char" LENGTH="50" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="vocational_description" TYPE="text" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="emotional_skills_level" TYPE="text" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="goal_short_term" TYPE="text" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="goal_medium_term" TYPE="text" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="goal_long_term" TYPE="text" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="action_short_term" TYPE="text" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="action_medium_term" TYPE="text" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="action_long_term" TYPE="text" NOTNULL="false" SEQUENCE="false"/>
        <FIELD NAME="is_completed" TYPE="int" LENGTH="1" NOTNULL="true" DEFAULT="0" SEQUENCE="false"/>
        <FIELD NAME="created_at" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="false"/>
        <FIELD NAME="updated_at" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="false"/>
      </FIELDS>
      <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
        <KEY NAME="user" TYPE="foreign" FIELDS="user" REFTABLE="user" REFFIELDS="id"/>
      </KEYS>
      <INDEXES>
        <INDEX NAME="user_idx" UNIQUE="true" FIELDS="user"/>
      </INDEXES>
    </TABLE>
    <TABLE NAME="block_student_path_history" COMMENT="Stores snapshots of student path by semester">
      <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true"/>
        <FIELD NAME="userid" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="false"/>
        <FIELD NAME="period" TYPE="char" LENGTH="20" NOTNULL="true" SEQUENCE="false" COMMENT="e.g. 2025-1, 2025-2"/>
        <FIELD NAME="content" TYPE="text" NOTNULL="true" SEQUENCE="false" COMMENT="JSON encoded content of the map"/>
        <FIELD NAME="timecreated" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="false"/>
      </FIELDS>
      <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
        <KEY NAME="userid" TYPE="foreign" FIELDS="userid" REFTABLE="user" REFFIELDS="id"/>
      </KEYS>
      <INDEXES>
        <INDEX NAME="userid_period_idx" UNIQUE="true" FIELDS="userid, period"/>
      </INDEXES>
    </TABLE>
  </TABLES>
</XMLDB>
