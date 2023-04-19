import { BlockViewProps, useGlobal } from '@metafox/framework';

export interface Props extends BlockViewProps {
  initialValues: any;
}

export default function Base(props: Props) {
  const { useContentParams, jsxBackend } = useGlobal();
  const { mainBlock } = useContentParams();

  if (!mainBlock?.component) return null;

  return jsxBackend.render(mainBlock);
}
