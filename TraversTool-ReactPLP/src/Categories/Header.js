import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

const Header = ({ category, cmsContent }) => {
  const descriptionClasses = classNames({
    description: true,
    'description--has-mobile': cmsContent.mobile_description,
  });

  return (
    <div>
      <h1>{category.name}</h1>
      <div className={descriptionClasses}>
        <div className="description__desktop" dangerouslySetInnerHTML={{ __html: cmsContent.description }} />
        <div className="description__mobile" dangerouslySetInnerHTML={{ __html: cmsContent.mobile_description }} />
      </div>
    </div>
  )
}

Header.propTypes = {
  category: PropTypes.object.isRequired,
  cmsContent: PropTypes.object,
}

export default Header;
