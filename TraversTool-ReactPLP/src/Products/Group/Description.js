import PropTypes from 'prop-types';
import React from 'react';

const Description = ({
  descriptions,
  ...props
}) => {
  // Replace any non-alphanumeric characters with - and make lowercase
  const format = value => {
    return value.replace(/[^0-9a-z]/gi,'-').toLowerCase();
  };

  const attribute = format(props.attribute);
  const value = format(props.value);

  if (!descriptions[attribute]) {
    return null;
  }

  const createMarkup = () => ({
    __html: descriptions[attribute][value],
  });

  return (
    <div dangerouslySetInnerHTML={createMarkup()} />
  );
};

Description.propTypes = {
  attribute: PropTypes.string.isRequired,
  descriptions: PropTypes.object.isRequired,
  value: PropTypes.string.isRequired,
};

export default Description;
