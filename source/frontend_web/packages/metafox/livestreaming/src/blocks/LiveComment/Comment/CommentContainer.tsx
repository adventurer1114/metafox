/**
 * @type: service
 * name: CommentItemViewLiveStreaming
 */

import { connectItemView } from '@metafox/framework';
import Comment from './Comment';

export default connectItemView(Comment, undefined, { extra_data: true });
