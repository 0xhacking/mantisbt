<?php
# MantisBT - a php based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.


require_once( 'core.php' );

#
# Class - TODO: move to timeline_api.php
#

class TimelineEvent {
	public $timestamp;
	public $user_id;

	public function html() {
		echo $timestamp;
	}

	public function format_timestamp( $p_timestamp ) {
		$t_normal_date_format = config_get( 'normal_date_format' );
		return date( $t_normal_date_format, $p_timestamp );
	}
}

class IssueCreatedTimelineEvent extends TimelineEvent {
	public $issue_id;

	public function valid() {
		return bug_exists( $this->issue_id );
	}

	public function html() {
		$t_avatar = user_get_avatar( $this->user_id, 32 );
		$t_avatar_url = $t_avatar[0];

		echo '<div class="entry">';
		echo '<img class="avatar" src="' . $t_avatar_url . '"/>';
		echo '<div class="timestamp">' .  $this->format_timestamp( $this->timestamp ) . '</div>';
		echo '<div class="action">' . sprintf( lang_get( 'timeline_issue_created' ), user_get_name( $this->user_id ), string_get_bug_view_link( $this->issue_id ) ) . '</div>';
		echo '</div>';
	}
}

class IssueNoteCreatedTimelineEvent extends TimelineEvent {
	public $issue_id;
	public $issue_note_id;

	public function valid() {
		return bugnote_exists( $this->issue_note_id );
	}

	public function html() {
		$t_avatar = user_get_avatar( $this->user_id, 32 );
		$t_avatar_url = $t_avatar[0];

		echo '<div class="entry">';
		echo '<img class="avatar" src="' . $t_avatar_url . '"/>';
		echo '<div class="timestamp">' .  $this->format_timestamp( $this->timestamp ) . '</div>';
		echo '<div class="action">' . sprintf( lang_get( 'timeline_issue_note_created' ), user_get_name( $this->user_id ), string_get_bug_view_link( $this->issue_id ) ) . '</div>';
		echo '</div>';
	}
}

class IssueMonitorTimelineEvent extends TimelineEvent {
	public $issue_id;
	public $monitor;

	public function valid() {
		return bug_exists( $this->issue_id );
	}

	public function html() {
		$t_string = $this->monitor ? lang_get( 'timeline_issue_monitor' ) : lang_get( 'timeline_issue_unmonitor' );

		$t_avatar = user_get_avatar( $this->user_id, 32 );
		$t_avatar_url = $t_avatar[0];

		echo '<div class="entry">';
		echo '<img class="avatar" src="' . $t_avatar_url . '"/>';
		echo '<div class="timestamp">' .  $this->format_timestamp( $this->timestamp ) . '</div>';
		echo '<div class="action">' . sprintf( $t_string, user_get_name( $this->user_id ), string_get_bug_view_link( $this->issue_id ) ) . '</div>';
		echo '</div>';
	}
}

class IssueTagTimelineEvent extends TimelineEvent {
	public $issue_id;
	public $tag_name;
	public $tag;

	public function valid() {
		return bug_exists( $this->issue_id );
	}

	public function html() {
		$t_string = $this->tag ? lang_get( 'timeline_issue_tagged' ) : lang_get( 'timeline_issue_tagged' );

		$t_avatar = user_get_avatar( $this->user_id, 32 );
		$t_avatar_url = $t_avatar[0];

		echo '<div class="entry">';
		echo '<img class="avatar" src="' . $t_avatar_url . '"/>';
		echo '<div class="timestamp">' .  $this->format_timestamp( $this->timestamp ) . '</div>';
		echo '<div class="action">' . sprintf( $t_string, user_get_name( $this->user_id ), string_get_bug_view_link( $this->issue_id ), $this->tag_name ) . '</div>';
		echo '</div>';
	}
}

class IssueStatusChangeTimelineEvent extends TimelineEvent {
	public $issue_id;
	public $old_status;
	public $new_status;

	public function valid() {
		return bug_exists( $this->issue_id );
	}

	public function html() {
		$t_resolved = config_get( 'bug_resolved_status_threshold' );
		$t_closed = config_get( 'bug_closed_status_threshold' );

		if ( $this->old_status < $t_closed && $this->new_status >= $t_closed ) {
			$t_string = sprintf( lang_get( 'timeline_issue_closed' ), user_get_name( $this->user_id ), string_get_bug_view_link( $this->issue_id ) );
		} else if ( $this->old_status < $t_resolved && $this->new_status >= $t_resolved ) {
			$t_string = sprintf( lang_get( 'timeline_issue_resolved' ), user_get_name( $this->user_id ), string_get_bug_view_link( $this->issue_id ) );
		} else if ( $this->old_status >= $t_resolved && $this->new_status < $t_resolved ) {
			$t_string = sprintf( lang_get( 'timeline_issue_reopened' ), user_get_name( $this->user_id ), string_get_bug_view_link( $this->issue_id ) );
		} else {
			return;
		}

		$t_avatar = user_get_avatar( $this->user_id, 32 );
		$t_avatar_url = $t_avatar[0];

		echo '<div class="entry">';
		echo '<img class="avatar" src="' . $t_avatar_url . '"/>';
		echo '<div class="timestamp">' .  $this->format_timestamp( $this->timestamp ) . '</div>';
		echo '<div class="action">' . $t_string . '</div>';
		echo '</div>';
	}
}

class IssueAssignedTimelineEvent extends TimelineEvent {
	public $issue_id;
	public $handler_id;

	public function valid() {
		return bug_exists( $this->issue_id );
	}

	public function html() {
		if ( $this->user_id == $this->handler_id ) {
			$t_string = sprintf( lang_get( 'timeline_issue_assigned_to_self' ), user_get_name( $this->user_id ), string_get_bug_view_link( $this->issue_id ) );
		} else {
			$t_string = sprintf( lang_get( 'timeline_issue_assigned' ), user_get_name( $this->user_id ), string_get_bug_view_link( $this->issue_id ), user_get_name( $this->handler_id ) );
		}

		$t_avatar = user_get_avatar( $this->user_id, 32 );
		$t_avatar_url = $t_avatar[0];

		echo '<div class="entry">';
		echo '<img class="avatar" src="' . $t_avatar_url . '"/>';
		echo '<div class="timestamp">' .  $this->format_timestamp( $this->timestamp ) . '</div>';
		echo '<div class="action">' . $t_string . '</div>';
		echo '</div>';
	}
}

function timeline_get_affected_issues( $p_start_time, $p_end_time ) {
	$t_mantis_bug_history_table = db_get_table( 'bug_history' );

	$query = "SELECT DISTINCT(bug_id) from $t_mantis_bug_history_table WHERE date_modified >= " . db_param() . " AND date_modified < " . db_param();
	$result = db_query_bound( $query, array( $p_start_time, $p_end_time ) );

	$t_current_project = helper_get_current_project();

	$t_issue_ids = array();
	while ( ( $t_row = db_fetch_array( $result ) ) !== false ) {
		$t_issue_id = $t_row['bug_id'];

		if ( $t_current_project != ALL_PROJECTS && $t_current_project != bug_get_field( $t_issue_id, 'project_id' ) ) {
			continue;
		}

		if ( !access_has_bug_level( VIEWER, $t_issue_id ) ) {
			continue;
		}

		$t_issue_ids[] = $t_issue_id;
	}

	return $t_issue_ids;
}

function timeline_events( $p_start_time, $p_end_time ) {
	$t_issue_ids = timeline_get_affected_issues( $p_start_time, $p_end_time );

	$t_timeline_events = array();

	foreach ( $t_issue_ids as $t_issue_id ) {
		$t_history_events_array = history_get_raw_events_array( $t_issue_id );
		$t_history_events_array = array_reverse( $t_history_events_array );

		foreach ( $t_history_events_array as $t_history_event ) {
			if ( $t_history_event['date'] < $p_start_time ||
				 $t_history_event['date'] >= $p_end_time ) {
				continue;
			}

			if ( $t_history_event['type'] == NEW_BUG ) {
				$t_event = new IssueCreatedTimelineEvent();
				$t_event->issue_id = $t_issue_id;
			} else if ( $t_history_event['type'] == BUGNOTE_ADDED ) {
				$t_event = new IssueNoteCreatedTimelineEvent();
				$t_event->issue_id = $t_issue_id;
				$t_event->issue_note_id = $t_history_event['old_value'];
			} else if ( $t_history_event['type'] == BUG_MONITOR ) {
				# Skip monitors added for others due to reminders, only add monitor events where added
				# user is the same as the logged in user.
				if ( (int)$t_history_event['old_value'] == (int)$t_history_event['userid'] ) {
					$t_event = new IssueMonitorTimelineEvent();
					$t_event->issue_id = $t_issue_id;
					$t_event->monitor = true;
				}
			} else if ( $t_history_event['type'] == BUG_UNMONITOR ) {
				$t_event = new IssueMonitorTimelineEvent();
				$t_event->issue_id = $t_issue_id;
				$t_event->monitor = false;
			} else if ( $t_history_event['type'] == TAG_ATTACHED ) {
				$t_event = new IssueTagTimelineEvent();
				$t_event->issue_id = $t_issue_id;
				$t_event->tag_name = $t_history_event['old_value'];
				$t_event->tag = true;
			} else if ( $t_history_event['type'] == TAG_DETACHED ) {
				$t_event = new IssueTagTimelineEvent();
				$t_event->issue_id = $t_issue_id;
				$t_event->tag_name = $t_history_event['old_value'];
				$t_event->tag = false;
			} else if ( $t_history_event['type'] == NORMAL_TYPE ) {
				switch ( $t_history_event['field'] ) {
					case 'status':
						$t_event = new IssueStatusChangeTimelineEvent();
						$t_event->issue_id = $t_issue_id;
						$t_event->old_status = $t_history_event['old_value'];
						$t_event->new_status = $t_history_event['new_value'];
						break;
					case 'handler_id':
						$t_event = new IssueAssignedTimelineEvent();
						$t_event->issue_id = $t_issue_id;
						$t_event->handler_id = $t_history_event['new_value'];
						break;
					default:
						$t_event = null;
						break;
				}
			} else {
				$t_event = null;
			}

			if ( $t_event != null ) {
				$t_event->user_id = $t_history_event['userid'];
				$t_event->timestamp = $t_history_event['date'];
				$t_timeline_events[] = $t_event;
			}
		}
	}

	return $t_timeline_events;
}

function timeline_sort_events( $p_events ) {
	$t_count = count( $p_events );
	$t_stable = false;

	while ( !$t_stable ) {
		$t_stable = true;

		for ( $i = 0; $i < $t_count - 1; ++$i ) {
			if ( $p_events[$i]->timestamp < $p_events[$i + 1]->timestamp ) {
				$t_temp = $p_events[$i];
				$p_events[$i] = $p_events[$i+1];
				$p_events[$i+1] = $t_temp;
				$t_stable = false;
			}
		}
	}

	return $p_events;
}

function timeline_print_events( $p_events ) {
	foreach ( $p_events as $t_event ) {
		if ( !$t_event->valid() ) {
			continue;
		}

		$t_event->html();
	}
}

#
# Page - TODO: move to my_view_page or keep as core/timeline_inc.php or core/timeline_view_inc.php
#

$f_days = gpc_get_int( 'days', 0 );

$t_end_time = time() - ( $f_days * 24 * 60 * 60 );
$t_start_time = $t_end_time - ( 7 * 24 * 60 * 60 );
$t_events = timeline_events( $t_start_time, $t_end_time );

echo '<div class="timeline">';

$t_heading = lang_get( 'timeline_title' );

echo '<div class="heading">' . $t_heading . '</div>';

$t_short_date_format = config_get( 'short_date_format' );

$t_next_days = ( $f_days - 7 ) > 0 ? $f_days - 7 : 0;
$t_prev_link = ' [<a href="my_view_page.php?days=' . ( $f_days + 7 ) . '">' . lang_get( 'prev' ) . '</a>]';

if ( $t_next_days != $f_days ) {
	$t_next_link = ' [<a href="my_view_page.php?days=' . $t_next_days . '">' . lang_get( 'next' ) . '<a/>]';
} else {
	$t_next_link = '';
}

echo '<div class="date-range">' . date( $t_short_date_format, $t_start_time ) . ' .. ' . date( $t_short_date_format, $t_end_time ) . $t_prev_link . $t_next_link . '</div>';
$t_events = timeline_sort_events( $t_events );

if ( count( $t_events ) > 0 ) {
	timeline_print_events( $t_events );
} else {
	echo '<p>' . $s_timeline_no_activity . '</p>';
}

echo '</div>';
